<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 04.01.19
 * Time: 21:20
 */

namespace Resolvers\Types;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;

abstract class AbstractResolve
{

//    abstract public static function entityNew(EntityManager $EM, ...);
//    abstract public static function entityUpdate(EntityManager $EM, ...);
//    abstract public static function entityDelete(...$args);

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
    protected static function updateListObject(PersistentCollection $oldObjects, array $newObjects): PersistentCollection
    {

        $newObjectsId = array_map(function($newObject){ return $newObject->getId();}, $newObjects);

        $oldObjects->filter(function($oldObject) use ($newObjectsId){

            return in_array($oldObject->getId(), $newObjectsId);
        });

        foreach ($newObjects as $newObject) {

            if (!$oldObjects->contains($newObject)) $oldObjects->add($newObject);
        }

        return $oldObjects;
    }
}