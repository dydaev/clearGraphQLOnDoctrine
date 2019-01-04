<?php
namespace Resolvers\Types;


use entities\ContactType;

class ContactsTypeResolve
{
    public static function createType(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['type'])) {//type: String!, regex: String, prefix: String

                $type = new ContactType();

                $type->setType($args['type']);

                if(!empty($args['regex'])) $type->setRegEx($args['regex']);
                if(!empty($args['prefix'])) $type->setPrefix($args['prefix']);

                $EM = $context['EntityManager'];
                $EM->persist($type);
                $EM->flush();

                return $type->getGraphArray();
            }
        };}

    public static function updateType(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {

                $EM = $context['EntityManager'];
                $type = $EM->getRepository('entities\ContactType')->find($args['id']);

                if (!empty($type)) {

                    if(!empty($args['type'])) $type->setRegEx($args['type']);
                    if(!empty($args['regex'])) $type->setRegEx($args['regex']);
                    if(!empty($args['prefix'])) $type->setPrefix($args['prefix']);

                    $EM->persist($type);
                    $EM->flush();

                    return $type->getGraphArray();
                }

            }
        };}

    public static function deleteType(){
        return function($root, $args, $context){
            if (empty($context['user'])) throw new Error("no authorized");

            if (!empty($args['id'])) {
                $EM = $context['EntityManager'];
                $type = $EM->getRepository('entities\ContactType')->find($args['id']);

                if (!empty($type)) {
                    $EM->remove($type);
                    $EM->flush();
                    return $type->getGraphArray();
                }
            }
        };}

    public static function getContactsTypeById(){
        return function($root, $args, $context)
    {
        if (empty($context['user'])) throw new Error("no authorized");

        $EM = $context['EntityManager'];

        $contactsType = $EM->getRepository('entities\ContactType')->find($args['id']);

        if (!empty($contactsType)) {
            return $contactsType->getGraphArray();
        }
    };}

    public static function getAllContactsType(){
        return function($root, $args, $context)
    {
        if (empty($context['user'])) throw new Error("no authorized");

        $EM = $context['EntityManager'];

        $contactsType = $EM->getRepository('entities\ContactType')->findAll();

        if (!empty($contactsType)) {
            return array_map(function($contactType){return $contactType->getGraphArray();},$contactsType);
        }
    };}
}