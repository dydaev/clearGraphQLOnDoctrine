<?php
namespace Resolvers\Types;


class CustomerResolve
{
    public static function getCustomer(){
        return function($root, $args, $context){

            $EM = $context['EntityManager'];

            $res = $EM->getRepository('entities\Customer')->findOneBy([ 'uuid' => $args['uuid'] ]);

            return $res;
    };}

    public static function getAllCustomers(){
        return function($root, $args, $context){
            $EM = $context['EntityManager'];

            $res = $EM->getRepository('entities\Customer')->findAll();

            return array_map(function($contact){return $contact->getGraphArray();},$res) ;
    };}


    public static function getCountOfCustomers(){
        return function($root, $args, $context){

        $res = self::getAllCustomers();
        return count($res()) ;
    };}
}