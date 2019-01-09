<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 04.01.19
 * Time: 21:20
 */

namespace Resolvers\Types;

use GraphQL\Error\Error;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

abstract class AbstractResolve
{

//    abstract public static function entityNew(...$params);
//    abstract public static function entityUpdate(EntityManager $EM, ...);
//    abstract public static function entityDelete(...$args);


    abstract public static function getAll();
    abstract public static function create();
    abstract public static function update();
    abstract public static function delete();


    public static function getCount(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $res = self::getAll();
            return !empty($res) ? count($res($root, $args, $context)) : 0 ;
    };}

    /**
    * @param $context
    *
    * @return EntityManager
    */
    protected static function getEntityManager( $context): EntityManager
    {
        return $context['EntityManager'];
    }

    /**
    * @param $context
    *
    * @return string username
    */
    protected static function getUser( $context)
    {
        return $context['user'];
    }

    /**
    * @param PersistentCollection $oldObjects
    * @param array $newObjects
    *
    * @return PersistentCollection updated collection
    */
    public static function updateListObject(PersistentCollection $oldObjects, array $newObjects): PersistentCollection
    {

        if (count($newObjects) > 0) {

            $newObjectsId = array_map(function($newObject){ return $newObject->getId();}, $newObjects);

            $oldObjects->filter(function($oldObject) use ($newObjectsId) {

                return in_array($oldObject->getId(), $newObjectsId);
            });

            foreach ($newObjects as $newObject) {

                if (!$oldObjects->contains($newObject)) $oldObjects->add($newObject);
            }
        } else $oldObjects->clear();

        return $oldObjects;
    }
}