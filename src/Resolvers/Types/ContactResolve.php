<?php
namespace Resolvers\Types;


class ContactResolve
{
    public static function getContact(){
        return function($root, $args, $context)
    {
        return [
            'uuid' => $args['uuid'],
            'type' => [],
            'value' => 'String'];
    };}
}