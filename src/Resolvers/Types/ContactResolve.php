<?php
namespace Resolvers\Types;


class ContactResolve
{
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