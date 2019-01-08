<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 04.01.19
 * Time: 17:01
 */

namespace Resolvers\Types;

use Doctrine\ORM\EntityManager;
use entities\Rule;
use GraphQL\Error\Error;

class RuleResolve extends AbstractResolve
{
    /**
     * @param EntityManager $EM
     * @param String $rulePath
     * @param String $permission
     * @param String $description
     *
     * @return Rule
     * @throws
     */
    public static function entityNew(EntityManager $EM, $rulePath, $permission, $description): Rule
    {
        if(!empty($rulePath) && !empty($permission)) {

            $rule = new Rule();

            try {

                $EM->persist($rule);

                $EM->flush();

                return self::entityUpdate($EM, $rule, $rulePath, $permission, $description);

            } catch (\Exception $e) {

                throw new \Exception($e->getMessage());

            }

        } else return null;
    }

    /**
     * @param EntityManager $EM
     * @param Rule $rule
     * @param String $rulePath
     * @param integer $permission
     * @param String $description
     *
     * @return Rule
     * @throws
     */
    public static function entityUpdate(EntityManager $EM, Rule $rule, $rulePath, int $permission, $description): Rule
    {
        if(!empty($rule)) {

            if (!empty($rulePath)) $rule->setRulePath($rulePath);

            if (!empty($permission)) $rule->setPermission($permission);

            if(!empty($description)) $rule->setDescription($description);

            $EM->persist($rule);

            $EM->flush();

            return $rule;
        }

        return null;
    }

    /**
     * @param EntityManager $EM
     * @param Rule $rule
     *
     * @return Rule
     * @throws
     */
    public static function entityDelete(EntityManager $EM, Rule $rule): Rule
    {
        if (!empty($rule)) {

            $EM->remove($rule);
            return $rule;
        }
        return null;
    }

/*
*********************************************
*/

    public static function getAll(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $res = $EM->getRepository('entities\Rule')->findAll();

            return array_map(function(Rule $rule){return $rule->getGraphArray();},$res) ;
        };
    }

    public static function getById(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $rule = $EM->getRepository('entities\Rule')->find( $args['id']);

                if (!empty($rule)) return $rule->getGraphArray();

                else throw new Error("rule not found");

            } else throw new Error("rule id is not specified");
        };
    }

    public static function create(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['rulePath'] && !empty($args['permission']))) {

                $EM = self::getEntityManager($context);

                try {

                    $result = self::entityNew($EM, $args['rulePath'], $args['permission'], $args['description']);

                } catch (\Exception $e) {

                    throw new Error("rule is not created: ". $e->getMessage());
                }


                if ($result) return $result->getGraphArray();

                throw new Error("rule did not create");

            } else throw new Error("rulePath or permission is not specified");

        };
    }

    public static function update(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $rule = $EM->getRepository('entities\Rule')->find($args['id']);

                if (!empty($rule) && $rule instanceof Rule) {

                    $result = self::entityUpdate($EM, $rule, $args['rulePath'], $args['permission'], $args['description']);

                    if ($result) return $result->getGraphArray();

                    throw new Error("rule did not update");

                } else throw new Error("rule is not found");

            } else throw new Error("no id to updating");

        };
    }

    public static function delete(){
        return function(/** @noinspection PhpUnusedParameterInspection */ $root, $args, $context) {
            if (empty($context['user'])) throw new Error("no authorized");

            if(!empty($args['id'])) {

                $EM = self::getEntityManager($context);

                $rule = $EM->getRepository('entities\Rule')->find($args['id']);

                if (!empty($rule) && $rule instanceof Rule) {

                    $res = self::entityDelete($EM, $rule);

                    return $res->getGraphArray();
                }
                else throw new Error("rule is not found");
            } else throw new Error("rule id is not specified");
        };
    }

}