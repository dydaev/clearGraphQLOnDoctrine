<?php
declare(strict_types=1);

namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';
use GraphQL\Error\Error;
use \Doctrine\ORM\EntityManager;
use \entities\Person;
use \entities\Contact;
use \entities\Customer;

class CustomerResolve
{
    /**
     * $EM : Entity Manager
     * $args : arguments for person and customer entityes (name, discount_card,tags, contacts ...etc)
     */
    public static function entityNew($EM, $args): Customer
    {
        $customer = new Customer();

        if(!empty($args['discount_card'])) $customer->setDiscount_card($args['discount_card']);

        $person = PersonResolve::entityNew($EM, $args);

        $customer->setPerson($person);

        $EM->persist($customer);

        $EM->flush();

        return $customer;
    }

    /**
     * @param EntityManager $EM : Entity Manager
     * @param mixed $args : arguments for person and customer entityes (name, discount_card,tags, contacts ...etc)
     * @return  Customer | null
     */
    public static function entityUpdate(EntityManager $EM, $args): Customer
    {
        if (!empty($args['uuid'])) {

            $person = PersonResolve::entityUpdate($EM, $args);

            if (!empty($person)) {

                $customer = $person->getCustomer();
                $isChangedCustomer = false;

                if(!empty($args['discount_card']) && $customer->getDiscount_card() !== $args['discount_card']) {

                    $customer->setDiscount_card($args['discount_card']);
                    $isChangedCustomer = true;
                }

                if ($isChangedCustomer){

                    $EM->persist($person);

                    $EM->flush();
                }

                return $customer;
            }// else throw new Error('contact for updating is not found');
        }
        return null;
    }

    /**
     * @param EntityManager $EM : Entity Manager
     * @param mixed $args : argument values uuid person
     * @return  Customer | null
     */
    public static function entityDelete(EntityManager $EM, $args): Customer
    {
        if (!empty($args['uuid'])) {

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {

                $customer = $person->getCustomer();
                $isChangedCustomer= false;

                if(true) {//if need removing other depends


                    $isChangedCustomer = true;
                }

                if ($isChangedCustomer){

                    $EM->remove($customer);

                    $EM->flush();

                    PersonResolve::entityDelete($EM, $args);
                }

                return $customer;
            }
        }
        return null;
    }

    public static function createCustomer(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            //There must be at least one contact.
            if (!empty($args['contacts']) && is_array($args['contacts']) && count($args['contacts']) > 0) {

                $EM = $context['EntityManager'];

                return self::entityNew($EM, $args)->getGraphArray();
            } else {
                throw new Error("Can`t create customer, need add contact");
            }
            
        };}

    public static function updateCustomer(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];
                $customer = self::entityUpdate($EM, $args);

                if (!empty($customer)) {

                    return $customer->getGraphArray();
                } else throw new Error("Can`t update customer, what went wrong");

            }
        };}

    public static function deleteCustomer(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];
                $customer = CustomerResolve::entityDelete($EM, $args);

                if (!empty($customer)) {

                    return $customer->getGraphArray();

                } else throw new Error("Can`t find customer for deleting");
            } else throw new Error("Need paste uuid for removing customer");
        };}

    public static function getCustomer(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = $context['EntityManager'];

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                return $person->getCustomer()->getGraphArray();
            }
    };}

    public static function getAllCustomers(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = $context['EntityManager'];

            $res = $EM->getRepository('entities\Customer')->findAll();

            return array_map(function($contact){return $contact->getGraphArray();},$res) ;
    };}


    public static function getCountOfCustomers(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $res = self::getAllCustomers();
            return !empty($res) ? count($res($root, $args, $context)) : 0 ;
    };}
}