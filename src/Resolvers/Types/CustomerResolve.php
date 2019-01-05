<?php
declare(strict_types=1);

namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';
use GraphQL\Error\Error;
use \Doctrine\ORM\EntityManager;
use \entities\Customer;

class CustomerResolve extends AbstractResolve
{
    /**
     * @param EntityManager $EM
     * @param  $args : arguments for person and customer entityes (name, discount_card,tags, contacts ...etc)
     *
     * @return  Customer
     * @throws
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
     * @param EntityManager $EM
     * @param mixed $args : arguments for person and customer entityes (name, discount_card,tags, contacts ...etc)
     * @return  Customer | null
     * @throws
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
            }
        }
        return null;
    }

    /**
     * @param EntityManager $EM
     * @param mixed $args : argument values uuid person
     * @return  Customer | null
     * @throws
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

    public static function create(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            //There must be at least one contact.
            if (!empty($args['contacts']) && is_array($args['contacts']) && count($args['contacts']) > 0) {

                $EM = self::getEntityManager($context);

                return self::entityNew($EM, $args)->getGraphArray();
            } else {
                throw new Error("Can`t create customer, need add contact");
            }
            
        };}

    public static function update(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = self::getEntityManager($context);
                $customer = self::entityUpdate($EM, $args);

                if (!empty($customer)) {

                    return $customer->getGraphArray();

                } else throw new Error("Can`t update customer, what went wrong");
            }
            throw new Error("no customer uuid to updating");
        };}

    public static function delete(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['uuid'])) {

                $EM = self::getEntityManager($context);
                $customer = CustomerResolve::entityDelete($EM, $args);

                if (!empty($customer)) {

                    return $customer->getGraphArray();

                } else throw new Error("Can`t find customer for deleting");
            } else throw new Error("Need paste uuid for removing customer");
        };}

    public static function getCustomerByUuid(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                return $person->getCustomer()->getGraphArray();
            }
            throw new Error("customer is not found");
    };}

    public static function getAll(){
        return function(/** @noinspection PhpUnusedParameterInspection */$root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            $EM = self::getEntityManager($context);

            $res = $EM->getRepository('entities\Customer')->findAll();

            return array_map(function(Customer $customer){return $customer->getGraphArray();},$res) ;
    };}

}