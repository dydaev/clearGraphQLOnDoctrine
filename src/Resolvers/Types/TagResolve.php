<?php
namespace Resolvers\Types;

class TagResolve{
    public static function getCountOfTags(){
        return function($root, $args, $context) {
        return $args['num'];
    };}

    public static function getAllTags(){
        return function($root, $args, $context) {
        $tag = [
        'id' => 1,
        'name' => 'String',
        'color' => 'String'];

        return [$tag];
    };}
}
