<?php
namespace Resolvers;

require_once __DIR__.'/../../vendor/autoload.php';

use Resolvers\Types\TagResolve;
use Resolvers\Types\CustomerResolve;
use Resolvers\Types\ContactsTypeResolve;
use Resolvers\Types\ContactResolve;


return [
    'Query' => [
        'allTags'=> TagResolve::getAllTags(),
        'countOfTags'=>  TagResolve::getCountOfTags(),

        'allContactTypes' => ContactsTypeResolve::getAllContactsType(),
        'contactType' => ContactsTypeResolve::getContactsType(),

        'allCustomers' => CustomerResolve::getAllCustomers(),
        'countOfCustomers' => CustomerResolve::getCountOfCustomers(),
        'customer' => CustomerResolve::getCustomer(),
    ]
];