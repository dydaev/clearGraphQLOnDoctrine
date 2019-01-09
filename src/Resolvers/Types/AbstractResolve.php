<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 04.01.19
 * Time: 21:20
 */

namespace Resolvers\Types;

use entities\ProtoForGraph;
use GraphQL\Error\Error;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Utils\Utils;

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

    /**
     * @param $context
     * @param $entityName
     * @return array
     */
    protected static function getUserAccess($context, $entityName) {
        $accessList = Utils::getUserAccessList(self::getEntityManager($context), $context['user'], $entityName);
        return  $accessList ? $accessList : null;
    }

    /**
     * @param $data
     * @param array $userAccessList
     * @param int $permissionMask
     * @param boolean $deep
     *
     * @return array
     */
    protected static function returnedData($data, $userAccessList, $permissionMask, $deep = false) {

        $getData = function($inData){

            if ($inData instanceof ProtoForGraph)

                $res = $inData->getGraphArray();
            else
                $res = $inData;

            return $res;
        };

        if ($data instanceof PersistentCollection) $data = $data->toArray();

        if (is_array($data))
            $result = array_map(function(ProtoForGraph $entityObj) use ($getData){return $getData($entityObj);},$data);
        else
            $result = $getData($data);

        return  Utils::checkRights($result, $userAccessList, $permissionMask, $deep);
    }
}