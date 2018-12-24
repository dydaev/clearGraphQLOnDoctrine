<?php
namespace Resolvers\Types;

class TagResolve{
    public static function getCountOfTags(){
        return function($root, $args, $context) {
            $tags = self::getAllTags();
            return !empty($tags) ? count($tags($root, $args, $context)) : 0 ;
    };}

    public static function getAllTags(){
        return function($root, $args, $context) {
            $EM = $context['EntityManager'];

            $tags = $EM->getRepository('entities\Tag')->findAll();

            return array_map(function($tag){return $tag->getGraphArray();},$tags) ;
    };}
}
