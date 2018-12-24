<?php
namespace Resolvers\Types;


class CustomerResolve
{
    public static function getCustomer(){
        return function($root, $args, $context)
    {
        return [
            'uuid' => 'String',
            'name' => 'String',
            'discount_card' => 5,
            'tags' => [tag],
            'contacts' => [contact],
        ];
    };}

    public static function getAllCustomers(){
        return function($root, $args, $context)
    {
        $res = self::getCustomer();
        return [ $res() ];
    };}


    public static function getCountOfCustomers(){
        return function($root, $args, $context)
    {
        $res = self::getAllCustomers();
        return count($res()) ;
    };}
}