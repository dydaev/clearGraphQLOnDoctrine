<?php
namespace Resolvers\Types;


class ContactsTypeResolve
{
    public static function getContactsType(){
        return function($root, $args, $context)
    {
        return [
            'id' => 2,
            'type' => 'String',
            'regex' => 'String',
            'prefix' => 'String'];
    };}

    public static function getAllContactsType(){
        return function($root, $args, $context)
    {
        return [ [
            'id' => 2,
            'type' => 'String',
            'regex' => 'String',
            'prefix' => 'String']];
    };}
}