<?php
declare(strict_types=1);

namespace Resolvers\Types;

use entities\Tag;

class TagResolve{

    /**
     * $EM : Entity Manager
     * $args : arguments for tag entity (name, color ...etc)
     */
    public static function entityNew($EM, $args) {
        
        $tag = $EM->getRepository('entities\Tag')->findOneBy([ 'name' => $args['name'] ]);

        if (empty($tag)) {

            $tag = new Tag();
            if(!empty($args['name'])) $tag->setName($args['name']);
            
            if(!empty($args['color'])) $tag->setColor($args['color']);
            
            $EM->persist($tag);
            $EM->flush();
        }
            
        return $tag;
    }

    /**
     * $EM : Entity Manager
     * $args : arguments for tag entity (name, color ...etc)
     */
    public static function entityUpdate($EM, $args) {
        if (!empty($args['id']) || !empty($args['name']) ) {

            $tag = !empty($args['id'])
                ? $EM->getRepository('entities\Tag')->find($args['id'])
                : $EM->getRepository('entities\Tag')->findOneBy([ 'name' => $args['name'] ]);

            if (!empty($tag)) {

                $isChanges = false;

                if(!empty($args['name']) && $tag->getName() !== $args['name']) {
                    $tag->setName($args['name']);
                    $isChanges = true;
                }

                if(!empty($args['color']) && $tag->getColor() !== $args['color']) {
                    $tag->setColor($args['color']);
                    $isChanges = true;
                }

                if ($isChanges) {
                    $EM->persist($tag);
                    $EM->flush();
                }

                return $tag;
            }
        }
        return null;
    }

    public static function createTag(){
        return function($root, $args, $context){
            if (!empty($args['name'])) {

                $EM = $context['EntityManager'];

                return self::entityNew($EM, $args)->getGraphArray();
            }
        };}

    public static function updateTag(){
        return function($root, $args, $context){
            if (!empty($args['id'])) {

                $EM = $context['EntityManager'];
                return self::entityUpdate($EM, $args)->getGraphArray();
            }
        };}

    public static function deleteTag(){
        return function($root, $args, $context){
            if (!empty($args['id'])) {
                $EM = $context['EntityManager'];
                $tag = $EM->getRepository('entities\Tag')->find($args['id']);

                if (!empty($tag)) {
                    $EM->remove($tag);
                    $EM->flush();
                    return $tag->getGraphArray();
                }
            }
        };}

    public static function getCountOfTags(){
        return function($root, $args, $context) {
            $tags = self::getAllTags();
            return !empty($tags) ? count($tags($root, $args, $context)) : 0;
        };}

    public static function getAllTags(){
        return function($root, $args, $context) {
            $EM = $context['EntityManager'];

            $tags = $EM->getRepository('entities\Tag')->findAll();

            return array_map(function($tag){return $tag->getGraphArray();},$tags);
        };}
}
