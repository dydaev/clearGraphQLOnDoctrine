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
    public static function entityNew($EM, $args): \entities\Customer
    {
        $customer = new Customer();

        $person = PersonResolve::entityNew($EM, $args);
        $customer->setPerson($person);

        if(!empty($args['discount_card'])) $customer->setDiscount_card($args['discount_card']);

        $EM->persist($customer);

        $EM->flush();

        return $customer->getGraphArray();
    }

    /**
     * $EM : Entity Manager
     * $args : arguments for person and customer entityes (name, discount_card,tags, contacts ...etc)
     */
    public static function entityUpdate($EM, $args): \entities\Customer
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
            } else throw new Error('contact for updating is not found');
        }
        return null;
    }

    public static function createCustomer(){
        return function($root, $args, $context){

            if (is_array($args['contacts']) && count($args['contacts'] > 0)) {

                $EM = $context['EntityManager'];

                return self::entityNew($EM, $args)->getGraphArray();
            } else {
                throw new Error("Can`t create customer, need add contact");
            }
            
        };}

    public static function updateCustomer(){
        return function($root, $args, $context){
            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];
                $contact = $EM->getRepository('entities\Customer')->findOneBy([ 'uuid' => $args['uuid']]);

                if (!empty($contact)) {

                    if(!empty($args['value'])) $contact->setValue($args['value']);

                    if(!empty($args['typeId'])) {
                        $type = $EM->getRepository('entities\ContactType')->find($args['typeId']);

                        if (!empty($type)) $contact->setType($type);
                    }
                    if(!empty($args['personUuid'])) {
                        $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['personUuid']]);

                        if (!empty($person)) $contact->setPerson($person);
                    }

                    $EM->persist($contact);
                    $EM->flush();

                    return $contact->getGraphArray();
                }

            }
        };}

    public static function deleteCustomer(){
        return function($root, $args, $context){
            if (!empty($args['uuid'])) {
                $EM = $context['EntityManager'];
                $contact = $EM->getRepository('entities\Contact')->findOneBy([ 'uuid' => $args['uuid']]);

                if (!empty($contact)) {
                    $EM->remove($contact);
                    $EM->flush();
                    return $contact->getGraphArray();
                }
            }
        };}

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
        return !empty($res) ? count($res($root, $args, $context)) : 0 ;
    };}
}