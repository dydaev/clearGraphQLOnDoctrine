<?php
namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

class CustomerResolve
{

    public static function getCustomer(){
        return function($root, $args, $context){

            $EM = $context['EntityManager'];

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                return $person->getCustomer()->getGraphArray();
            }
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
        return  !empty($res) ? count($res($root, $args, $context)) : 0 ;
    };}
}