<?php
namespace Resolvers;

require_once __DIR__.'/../../vendor/autoload.php';

use Resolvers\Types\TagResolve;
use Resolvers\Types\CustomerResolve;
use Resolvers\Types\ContactsTypeResolve;
use Resolvers\Types\ContactResolve;


return [
    'Query' => [
        'test' => function($root, $args, $context){return array_keys($context);},

        'allTags'=> TagResolve::getAllTags(),
        'countOfTags'=>  TagResolve::getCountOfTags(),

        'allContactTypes' => ContactsTypeResolve::getAllContactsType(),
        'contactType' => ContactsTypeResolve::getContactsType(),

        'contactById' => ContactResolve::getContact(),

        'allCustomers' => CustomerResolve::getAllCustomers(),
        'countOfCustomers' => CustomerResolve::getCountOfCustomers(),
        'customerById' => CustomerResolve::getCustomer(),
    ]
];