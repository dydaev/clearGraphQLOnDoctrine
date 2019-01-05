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

            $EM->persist($role);

            $EM->flush();

            return self::entityUpdate($EM, $role, $name, $description, $rules);

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

            if (!empty($name)) $role->setName($name);

            if(!empty($description)) $role->setDescription($description);

            if(!empty($newRules)) {

                $oldRules = $role->getRules();

                $updatedRules = self::updateListObject($oldRules, $newRules);

                $role->setAccessList($updatedRules);

            } else {
                $role->clearRules();
            }

            $EM->persist($role);

            $EM->flush();

            return $role;
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

            $EM->remove($role);
            return $role;
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

    public static function create(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['name'])) {

                $EM = self::getEntityManager($context);

                $rules = [];

                if(!empty($args['rulesId'])) {

                    foreach ($args['rulesId'] as $ruleId) {

                        $rule = $EM->getRepository('entities\Rule')->find($ruleId);

                        if(!empty($rule)) {

                            array_push($rules, $rule);

                        }
                    }
                }

                $result = self::entityNew($EM, $args['name'], $args['description'], $rules);

                if ($result) return $result->getGraphArray();

                throw new Error("role did not create");

            } else throw new Error("role name not specified");

        };
    }

    public static function update(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['roleId'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->find($args['roleId']);

                if (!empty($role) && $role instanceof Role) {

                    $rules = [];

                    if(!empty($args['rulesId'])) {

                        foreach ($args['rulesId'] as $ruleId) {

                            $rule = $EM->getRepository('entities\Rule')->find($ruleId);

                            if(!empty($rule)) {

                                array_push($rules, $rule);

                            }
                        }
                    }

                    $result = self::entityUpdate($EM, $role, $args['name'], $args['description'], $rules);

                    if ($result) return $result->getGraphArray();

                    throw new Error("role did not update");

                } else throw new Error("role is not found");

            } else throw new Error("no roleId to updating");

        };
    }

    public static function delete(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context) {
            if (empty($context['user'])) throw new Error("no authorized");

            if(!empty($args['roleId'])) {

                $EM = self::getEntityManager($context);

                $role = $EM->getRepository('entities\Role')->find($args['roleId']);

                if (!empty($role) && $role instanceof Role) {

                    $res = self::entityDelete($EM, $role);

                    return $res->getGraphArray();
                }
                else throw new Error("role is not found");
            } else throw new Error("no roleId to removing");
        };
    }

}