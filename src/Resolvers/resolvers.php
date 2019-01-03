<?php
namespace Resolvers;

require_once __DIR__.'/../../vendor/autoload.php';

use Resolvers\Types\TagResolve;
use Resolvers\Types\CustomerResolve;
use Resolvers\Types\ContactsTypeResolve;
use Resolvers\Types\ContactResolve;
use Resolvers\Types\UserResolve;


return [
    'Query' => [
        'test' => 'testRequest',

        'allTags'=> TagResolve::getAllTags(),
        'countOfTags'=>  TagResolve::getCountOfTags(),

        'allContactsTypes' => ContactsTypeResolve::getAllContactsType(),
        'contactsTypeById' => ContactsTypeResolve::getContactsTypeById(),

        'contactById' => ContactResolve::getContact(),

        'allCustomers' => CustomerResolve::getAllCustomers(),
        'countOfCustomers' => CustomerResolve::getCountOfCustomers(),
        'customerById' => CustomerResolve::getCustomer(),

        'allUsers' => UserResolve::getAllUsers(),
        'countOfUsers' => UserResolve::getCountOfUsers(),
        'userById' => UserResolve::getUser(),
    ],
    'Mutation' => [
        'createTag'=> TagResolve::createTag(),
        'updateTag'=> TagResolve::updateTag(),
        'deleteTag'=> TagResolve::deleteTag(),

        'createContactsType'=> ContactsTypeResolve::createType(),
        'updateContactsType'=> ContactsTypeResolve::updateType(),
        'deleteContactsType'=> ContactsTypeResolve::deleteType(),

        'createContact'=> ContactResolve::createContact(),
        'createContactForPerson'=> ContactResolve::getContact(),
        'updateContact'=> ContactResolve::updateContact(),
        'deleteContact'=> ContactResolve::deleteContact(),

        'addNewTagToCustomer'=> '',
        'addTagByIdToCustomer'=> '',
        'deleteTagOfCustomer'=> '',
        'addContactToCustomer'=> ContactResolve::updateContact(),
        'createCustomer'=> CustomerResolve::createCustomer(),
        'updateCustomer'=> CustomerResolve::updateCustomer(),
        'deleteCustomer'=> CustomerResolve::deleteCustomer(),

        'addContactToUser'=> ContactResolve::updateContact(),
        'createUser'=> UserResolve::createUser(),
        'updateUser'=> UserResolve::updateUser(),
        'deleteUser'=> UserResolve::deleteUser(),
    ]
];