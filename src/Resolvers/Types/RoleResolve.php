<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 04.01.19
 * Time: 17:01
 */

namespace Resolvers\Types;

use Doctrine\ORM\EntityManager;
use entities\Role;
use GraphQL\Error\Error;

class RoleResolve extends AbstractResolve
{
    /**
     * @param EntityManager $EM
     * @param String $name
     * @param String $description
     * @param array $rules list
     *
     * @return Role
     * @throws
     */
    public static function entityNew(EntityManager $EM, $name, $description, $rules): Role
    {
        if(!empty($name)) {

            $role = new Role();

            $role->setName($name);

            $EM->persist($role);

            $EM->flush();

            return self::entityUpdate($EM, $role, null, $description, $rules);

        } else return null;
    }

    /**
     * @param EntityManager $EM
     * @param Role $role
     * @param String $name
     * @param String $description
     * @param array $newRules list
     *
     * @return Role
     * @throws
     */
    public static function entityUpdate(EntityManager $EM, Role $role, $name, $description, $newRules): Role
    {
        if(!empty($role)) {

            if (isset($name) && $name !== $role->getName()) $role->setName($name);

            if(isset($description) && $description !== $role->getDescription()) $role->setDescription($description);

            if(isset($newRules)) {

                $oldRules = $role->getRules();

                $updatedRules = self::updateListObject($oldRules, $newRules);

                $role->setAccessList($updatedRules);
            }

            try {

                $EM->persist($role);
                $EM->flush();

                return $role;

            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }

        }

        return null;
    }

    /**
     * @param EntityManager $EM
     * @param Role $role
     *
     * @return Role
     * @throws
     */
    public static function entityDelete(EntityManager $EM, Role $role): Role
    {
        if (!empty($role)) {

            try {

                $EM->remove($role);
                $EM->flush();
                return $role;
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }
        return null;
    }


/*
********************
 */

    public static function getAll(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $res = $EM->getRepository('entities\Role')->findAll();
//print_r($res[0]->getGraphArray());
            return array_map(function(Role $role){return $role->getGraphArray();},$res) ;
        };
    }

    public static function getByName(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['name'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->findOneBy([ 'name' => $args['name'] ]);

                if (!empty($role)) return $role->getGraphArray();

                else throw new Error("role not found");

            } else throw new Error("role name not specified");
        };
    }

    public static function getById(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->findOneBy([ 'name' => $args['id'] ]);

                if (!empty($role)) return $role->getGraphArray();

                else throw new Error("role not found");

            } else throw new Error("role id not specified");
        };
    }

    public static function isRoleUsed(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if(!empty($args['name'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->findOneBy([ 'name' => $args['name'] ]);

                if (empty($role)) return true;

                return false;

            } else throw new Error("role name is empty");
        };
    }

    public static function create(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['name'])) {

                $EM = self::getEntityManager($context);

                $roleChecker = self::isRoleUsed();

                if ($roleChecker($root, $args, $context)) {

                    $rules = [];

                    if (!empty($args['rulesId'])) {

                        foreach ($args['rulesId'] as $ruleId) {

                            $rule = $EM->getRepository('entities\Rule')->find($ruleId);

                            if (!empty($rule)) {

                                array_push($rules, $rule);

                            }
                        }
                    }
                    $result = self::entityNew($EM, $args['name'], $args['description'], $rules);

                    if ($result) return $result->getGraphArray();

                    throw new Error("role did not create");

                } else throw new Error("name for role is used, select another name");

            } else throw new Error("role name not specified");

        };
    }

    public static function update(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->find($args['id']);

                if (!empty($role) && $role instanceof Role) {

                    try {

                        return self::entityUpdate($EM, $role, $args['name'], $args['description'], null)->getGraphArray();

                    } catch (\Exception $e) {

                        throw new Error("role did not update");
                    }
                } else throw new Error("role is not found");

            } else throw new Error("no roleId to updating");

        };
    }

    public static function updateRules(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['roleId'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->find($args['roleId']);

                if (!empty($role) && $role instanceof Role) {

                    $rules = [];

                    if(isset($args['rulesId'])) {

                        foreach ($args['rulesId'] as $ruleId) {

                            $rule = $EM->getRepository('entities\Rule')->find($ruleId);

                            if(!empty($rule)) array_push($rules, $rule);
                        }
                    }

                    try {

                        return self::entityUpdate($EM, $role, null, null, $rules)->getGraphArray();

                    } catch (\Exception $e) {

                        throw new Error("role did not update");
                    }
                } else throw new Error("role is not found");

            } else throw new Error("no roleId to updating");

        };
    }

    public static function delete(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context) {
            if (empty($context['user'])) throw new Error("no authorized");

            if(!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->find($args['id']);

                if (!empty($role) && $role instanceof Role) {

                    try {

                        $res = self::entityDelete($EM, $role);
                        return $res->getGraphArray();

                    } catch (\Exception $e) {
                        throw new Error("delete role is failed");
                    }

                }
                else throw new Error("role is not found");
            } else throw new Error("no roleId to removing");
        };
    }

}