<?php
declare(strict_types=1);

namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

use entities\Person;
use entities\User;
use GraphQL\Error\Error;
use \Doctrine\ORM\EntityManager;
use Utils\Utils;

class UserResolve extends AbstractResolve
{
    /**
     * @param EntityManager $EM
     * @param Person $person
     * @param string $login
     * @param string $password
     *
     * @return  User
     * @throws
     */
    public static function entityNew(EntityManager $EM, Person $person, $login, $password): User
    {
        if (!empty($login) && !empty($password)) {

            $user = new User();

            $user->setLogin($login);
            $user->setPassword($password);

            $user->setPerson($person);

            $EM->persist($user);

            $EM->flush();

            return $user;
        }
        return null;
    }

    /**
     * @param EntityManager $EM
     * @param Person $person
     * @param string $name
     * @param string $login
     * @param array $tags
     * @param array $contacts
     * @param array $roles
     *
     * @return  User | null
     * @throws
     */
    public static function entityUpdate(EntityManager $EM, $person, $name, $login, $tags, $contacts, $roles): User
    {

            if (!empty($person) && $person instanceof Person) {

                $user = $person->getUser();

                if ($user && $user instanceof User){

                    if(isset($login) && $user->getLogin() !== $login) {

                        $user->setLogin($login);
                        $EM->persist($user);
                    }

                    if(isset($name) && $person->getName() !== $name) {

                        $person->setName($name);
                        $EM->persist($person);
                    }

                    if(isset($roles)) {

                        $oldRules = $user->getRoles();

                        $updatedRoles = self::updateListObject($oldRules, $roles);

                        $user->setRoles($updatedRoles);
                        $EM->persist($user);

                    }

                    if(isset($contacts)) {

                        $oldContacts = $person->getContacts();

                        $updatedContacts = self::updateListObject($oldContacts, $contacts);

                        $person->setContacts($updatedContacts);
                        $EM->persist($person);

                    }

                    if(isset($tags)) {

                        $oldTags = $person->getTags();

                        $updatedTags = self::updateListObject($oldTags, $tags);

                        $person->setTags($updatedTags);
                        $EM->persist($person);

                    }

                    $EM->flush();

                    return $user;
                }
            }

        return null;
    }

    /**
     * @param EntityManager $EM
     * @param Person $person
     *
     * @return  User | null
     * @throws
     */
    public static function entityDelete(EntityManager $EM, Person $person): User
    {
        if (!empty($person)) {

            $user = $person->getUser();
            $customer = $person->getCustomer();

            if(empty($customer)) {

                return empty(PersonResolve::entityDelete($EM, [ 'uuid' => $person->getUUID()] )) ? null : $user;
            }

//            $person->setUser(null);

            $EM->remove($user);

            $EM->flush();

            return $user;
        }

        return null;
    }

    public static function create(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            //if (empty($context['user'])) throw new Error("no authorized");

            if(!empty($args['login'])) {
                if(!empty($args['password'])){
//                    if (!empty($args['contacts']) && is_array($args['contacts']) && count($args['contacts']) > 0) {

                        $EM = self::getEntityManager($context);

                        $user = $EM->getRepository('entities\User')->findOneBy([ 'login' => $args['login'] ]);

                        if (empty($user)) {

                            if (!empty($args['uuid']))
                                $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

                            if (empty($person)) $person = new Person();

                            $user = self::entityNew($EM, $person, $args['login'], $args['password'] )->getGraphArray();

                            if (!empty($args['contacts']) || !empty($args['tags']) || !empty($args['name']))
                                $user = self::entityUpdate($EM, $person, $args['name'], null, $args['tags'], $args['contacts'], null );

                            if (empty($user)) throw new Error("Can`t create user, what went wrong");

                            return $user;
                        }

                        else
                            throw new Error("Can`t create user, the login is used");

//                    } else throw new Error("Can`t create user, need add contact");

                } else throw new Error("Can`t create user without password");

            } else throw new Error("Can`t create user without login");
        };}

    public static function update(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = self::getEntityManager($context);

                $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

                if (!empty($person) && $person instanceof Person) {

                    $user = self::entityUpdate($EM, $person, $args['name'], $args['login'], $args['tags'], $args['contacts'], null);

                    if (!empty($user)) {

                        return $user->getGraphArray();
                    }
                    else throw new Error("Can`t update user, what went wrong");
                }
                else throw new Error("user is not found");
            }
            throw new Error("no user uuid to updating");
        };}

    public static function updateRoles(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = self::getEntityManager($context);

                $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

                if (!empty($person) && $person instanceof Person) {

                    $user = self::entityUpdate($EM, $person, $args['name'], $args['login'], $args['tags'], $args['contacts'], $args['roles']);

                    if (!empty($user)) {

                        return $user->getGraphArray();
                    }
                    else throw new Error("Can`t update user, what went wrong");
                }
                else throw new Error("user is not found");
            }
            throw new Error("no user uuid to updating");
        };}

    public static function delete(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = self::getEntityManager($context);

                                $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

                if (!empty($person) && $person instanceof Person) {

                    $user = self::entityDelete($EM, $person);

                    if (!empty($user)) {

                        return $user->getGraphArray();
                    }
                    else throw new Error("Can`t update user, what went wrong");
                }
                else throw new Error("user is not found");

            } else throw new Error("Need paste uuid for removing user");
        };}

    public static function getUserByUuid(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                return $person->getUser()->getGraphArray();
            }
            throw new Error("user is not found");
    };}

    public static function getAll(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $res = $EM->getRepository('entities\User')->findAll();

            return array_map(function(User $user){return $user->getGraphArray();},$res) ;
    };}

    public static function authorization(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){

            $EM = self::getEntityManager($context);
            if(!empty($args['login']) && !empty($args['password'])) {

                $user = $EM->getRepository('entities\User')->findOneBy([ 'login' => $args['login'] ]);

                if (!empty($user)) {
                    if ($user->checkPassword($args['password']))
                    {
                        $tokenArr = Utils::getToken();

                        $_SESSION['life'] = [
                            'user_login' => $user->getLogin(),
                            'uusr' => $user->getPerson()->getUUID(),
                            'key_of_life' => $tokenArr['token'],
                            'die_time' => $tokenArr['die_time']
                        ];

                        return [
                            'token' => $tokenArr['token'],
                            'life_time' => date(DATE_ATOM ,$tokenArr['die_time'])
                        ];
                    }
                }
                throw new Error("Login or password is wrong");

            } else throw new Error("Login or password is empty");
    };}

    public static function update_token(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);
            if (!empty($args['token'])) {

                if (Utils::checkToken($args['token'])) {

                    $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $_SESSION['life']['uusr'] ]);

                    if (!empty($person)) {

                        $newToken = Utils::getToken();

                        $_SESSION['life'] = [
                            'user_login' => $person->getUser()->getLogin(),
                            'uusr' => $person->getUUID(),
                            'key_of_life' => $newToken['token'],
                            'die_time' => $newToken['die_time']
                        ];

                        return [
                            'token' => $newToken['token'],
                            'life_time' => date(DATE_ATOM ,$newToken['die_time'])
                        ];
                    }
                }

            }

            return null;
    };}
}
