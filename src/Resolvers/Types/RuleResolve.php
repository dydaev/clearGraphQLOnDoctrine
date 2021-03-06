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
     * @param String $essence
     * @param String $rulePath
     * @param String $permission
     * @param String $description
     *
     * @return Rule
     * @throws
     */
    public static function entityNew(EntityManager $EM, $essence, $rulePath, $permission, $description): Rule
    {
        if(!empty($rulePath) && !empty($permission)) {

            $rule = new Rule();

            try {

                $rule->setEssence($essence);

                $rule->setRulePath($rulePath);

                $rule->setPermission($permission);

                $rule->setDescription($description);

                $EM->persist($rule);

                $EM->flush();

                return $rule;

            } catch (\Exception $e) {

                throw new \Exception($e->getMessage());

            }

        } else return null;
    }

    /**
     * @param EntityManager $EM
     * @param Rule $rule
     * @param String $essence
     * @param String $rulePath
     * @param integer $permission
     * @param String $description
     *
     * @return Rule
     * @throws
     */
    public static function entityUpdate(EntityManager $EM, Rule $rule, $essence, $rulePath, int $permission, $description): Rule
    {
        if(!empty($rule)) {

            try {

                if (isset($rulePath) && $rule->getRulePath() !== $rulePath ) $rule->setRulePath($rulePath);

                if (isset($essence) && $rule->getEssence() !== $essence ) $rule->setEssence($essence);

                if (isset($permission) && $rule->getPermission() !== $permission) $rule->setPermission($permission);

                if(isset($description) && $rule->getDescription() !== $description) $rule->setDescription($description);

                $EM->persist($rule);

                $EM->flush();

                return $rule;

            } catch (\Exception $e) {

                throw new \Exception($e->getMessage());

            }
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

            try {

                $EM->remove($rule);
                $EM->flush();

                return $rule;

            } catch (\Exception $e) {

                throw new \Exception($e->getMessage());
            }
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

                    $result = self::entityNew($EM, $args['essence'], $args['rulePath'], $args['permission'], $args['description']);

                    return $result->getGraphArray();

                } catch (\Exception $e) {

                    throw new Error("rule is not created, what want wrong");
                }

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

                    $result = self::entityUpdate($EM, $rule, $args['essence'], $args['rulePath'], $args['permission'], $args['description']);

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

                    try {

                        $res = self::entityDelete($EM, $rule);

                        return $res->getGraphArray();

                    } catch (\Exception $e) {

                        throw new Error("delete rule is failed, what want wrong");
                    }

                }
                else throw new Error("rule is not found");
            } else throw new Error("rule id is not specified");
        };
    }

}