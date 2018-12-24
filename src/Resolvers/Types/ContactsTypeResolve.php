<?php
namespace Resolvers\Types;


class ContactsTypeResolve
{
    public static function getContactsTypeById(){
        return function($root, $args, $context)
    {
        $EM = $context['EntityManager'];

        $contactsType = $EM->getRepository('entities\ContactType')->find($args['id']);

        if (!empty($contactsType)) {
            return $contactsType->getGraphArray();
        }
    };}

    public static function getAllContactsType(){
        return function($root, $args, $context)
    {
        $EM = $context['EntityManager'];

        $contactsType = $EM->getRepository('entities\ContactType')->findAll();

        if (!empty($contactsType)) {
            return array_map(function($contactType){return $contactType->getGraphArray();},$contactsType);
        }
    };}
}