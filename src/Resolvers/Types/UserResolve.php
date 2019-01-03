<?php
declare(strict_types=1);

namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

use entities\User;
use GraphQL\Error\Error;
use \Doctrine\ORM\EntityManager;
use \entities\Person;
use \entities\Contact;
use \entities\Customer;

class UserResolve
{
    /**
     * $EM : Entity Manager
     * $args : arguments for person and User entityes (name, login, password, tags, contacts ...etc)
     */
    public static function entityNew($EM, $args): User
    {
        $user = new User();

        if(!empty($args['login'])) $user->setLogin($args['login']);
        if(!empty($args['password'])) $user->setPassword($args['password']);

        $person = PersonResolve::entityNew($EM, $args);

        $user->setPerson($person);

        $EM->persist($user);

        $EM->flush();

        return $user;
    }

    /**
     * @param EntityManager $EM : Entity Manager
     * @param mixed $args : arguments for person and user entityes (name, login, password, tags, contacts ...etc)
     * @return  User | null
     */
    public static function entityUpdate(EntityManager $EM, $args): User
    {
        if (!empty($args['uuid'])) {

            $person = PersonResolve::entityUpdate($EM, $args);

            if (!empty($person)) {

                $user = $person->getUser();
                $isChangedCustomer = false;

                if(!empty($args['login']) && $user->getLogin() !== $args['login']) {

                    $user->setLogin($args['login']);
                    $isChangedCustomer = true;
                }

                if(!empty($args['password'])) {

                    $user->setPassword($args['password']);
                    $isChangedCustomer = true;
                }

                if ($isChangedCustomer){

                    $EM->persist($person);

                    $EM->flush();
                }

                return $user;
            }
        }
        return null;
    }

    /**
     * @param EntityManager $EM : Entity Manager
     * @param mixed $args : argument values uuid person
     * @return  User | null
     */
    public static function entityDelete(EntityManager $EM, $args): User
    {
        if (!empty($args['uuid'])) {

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {

                $user = $person->getUser();
                $isChangedCustomer= false;

                if(true) {//if need removing other depends


                    $isChangedCustomer = true;
                }

                if ($isChangedCustomer){

                    $EM->remove($user);

                    $EM->flush();

                    PersonResolve::entityDelete($EM, $args);
                }

                return $user;
            }
        }
        return null;
    }

    public static function createUser(){
        return function($root, $args, $context){

            if(!empty($args['login'])) {
                if(!empty($args['password'])){
                    if (!empty($args['contacts']) && is_array($args['contacts']) && count($args['contacts']) > 0) {

                        $EM = $context['EntityManager'];

                        return self::entityNew($EM, $args)->getGraphArray();

                    } else throw new Error("Can`t create user, need add contact");

                } else throw new Error("Can`t create user without password");

            } else throw new Error("Can`t create user without login");
        };}

    public static function updateUser(){
        return function($root, $args, $context){
            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];
                $user = self::entityUpdate($EM, $args);

                if (!empty($user)) {

                    return $user->getGraphArray();
                } else throw new Error("Can`t update user, what went wrong");

            }
        };}

    public static function deleteUser(){
        return function($root, $args, $context){
            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];
                $user = CustomerResolve::entityDelete($EM, $args);

                if (!empty($user)) {

                    return $user->getGraphArray();

                } else throw new Error("Can`t find user for deleting");
            } else throw new Error("Need paste uuid for removing user");
        };}

    public static function getUser(){
        return function($root, $args, $context){

            $EM = $context['EntityManager'];

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                return $person->getUser()->getGraphArray();
            }
    };}

    public static function getAllUsers(){
        return function($root, $args, $context){
            $EM = $context['EntityManager'];

            $res = $EM->getRepository('entities\User')->findAll();

            return array_map(function($user){return $user->getGraphArray();},$res) ;
    };}

    public static function authorization(){
        return function($root, $args, $context){
            $EM = $context['EntityManager'];

            if(!empty($args['login']) && !empty($args['password'])) {

                $res = $EM->getRepository('entities\User')->findAll();
                return ;//token

            } else throw new Error("Login or password is empty");
    };}

    public static function reauthorization(){
        return function($root, $args, $context){
            $EM = $context['EntityManager'];

            $res = $EM->getRepository('entities\User')->findAll();

            return ;//token
    };}

    public static function getCountOfUsers(){
        return function($root, $args, $context){

        $res = self::getAllUsers();
        return !empty($res) ? count($res($root, $args, $context)) : 0 ;
    };}
}