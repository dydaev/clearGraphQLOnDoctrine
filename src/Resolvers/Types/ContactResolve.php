<?php
namespace Resolvers\Types;

use entities\Contact;
use entities\ContactType;
use GraphQL\Error\Error;

class ContactResolve
{
    /**
     * $EM : Entity Manager
     * $args : arguments for contact entityes (typeId, value, personUuid ...etc)
     */
    public static function entityNew($EM, $args): \entities\Contact {
        if (!empty($args['typeId']) && !empty($args['value'])) {

            $type = $EM->getRepository('entities\ContactType')->find($args['typeId']);
            if (!empty($type)) {
                if (empty($type->getRegEx()) || (!empty($type->getRegEx()) && preg_match($type->getRegEx(), $args['value']))) {

                    $contact = new Contact();

                    $contact->setType($type);
                    $contact->setValue($args['value']);

                    if (!empty($args['personUuid'])) {
                        $person = $EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['personUuid']]);
                        $contact->setPerson($person);
                    }

                    $EM->persist($contact);
                    $EM->flush();

                    return $contact;
                } else throw new Error("contact invalid");
            }
        }
        return null;
    }

    /**
     * $EM : Entity Manager
     * $args : arguments for contact entity (typeId, value, personUuid ...etc)
     */
    public static function entityUpdate($EM, $args): \entities\Contact {
        if (!empty($args['uuid'])) {

            $contact = $EM->getRepository('entities\Contact')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($contact)) {

                $isChanges = false;

                if(!empty($args['typeId']) && $contact->getType()->getId() !== $args['typeId']) {

                    $type = $EM->getRepository('entities\ContactType')->find($args['typeId']);

                    if (!empty($type)) {

                        $contact->setType($type);
                        $isChanges = true;
                    } else throw new Error('incorrect type for contact');
                }

                if(!empty($args['value']) && $contact->getValue() !== $args['value']) {
                    $contact->setValue($args['color']);
                    $isChanges = true;
                }

                if(!empty($args['personUuid']) && $contact->getPerson()->getUUID() !== $args['personUuid']) {

                    $person = $EM->getRepository('entities\Person')->find($args['typeId']);

                    if (!empty($person)) {

                        $contact->setValue($person);
                        $isChanges = true;
                    } else throw new Error('incorrect person for contact');
                }

                if ($isChanges) {
                    $EM->persist($contact);
                    $EM->flush();
                }

                return $contact;
            } else throw new Error('contact for updating is not found');
        }
        return null;
    }

    public static function createContact(){ //uuid: String!, typeId: Int, value: String
        return function($root, $args, $context){
                $EM = $context['EntityManager'];

                return self::entityNew($EM, $args)->getGraphArray();

        };}

    public static function updateContact(){
        return function($root, $args, $context){
            if (!empty($args['uuid'])) {

                $EM = $context['EntityManager'];

                return self::entityUpdate($EM, $args);

            }
        };}

    public static function deleteContact(){
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

    public static function getContact(){
        return function($root, $args, $context)
        {

            $EM = $context['EntityManager'];

            $contact = $EM->getRepository('entities\Contact')->findOneBy([ 'uuid' => $args['uuid'] ]);
            if (!empty($contact)) {
                return $contact->getGraphArray();
            }

        };}
}