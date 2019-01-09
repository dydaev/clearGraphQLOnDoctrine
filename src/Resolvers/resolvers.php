<?php
namespace Resolvers;

require_once __DIR__.'/../../vendor/autoload.php';

use Resolvers\Types\RoleResolve;
use Resolvers\Types\RuleResolve;
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

        'allCustomers' => CustomerResolve::getAll(),
        'countOfCustomers' => CustomerResolve::getCount(),
        'customerById' => CustomerResolve::getCustomerByUuid(),

        'allUsers' => UserResolve::getAll(),
        'countOfUsers' => UserResolve::getCount(),
        'userById' => UserResolve::getUserByUuid(),
        'authorization' => UserResolve::authorization(),
        'update_token' => UserResolve::update_token(),

        'allRoles' => RoleResolve::getAll(),
        'getRole' => RoleResolve::getById(),
        'getRoleByName' => RoleResolve::getByName(),

        'allRules' => RuleResolve::getAll(),
        'getRuleById' => RuleResolve::getById(),

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
        'createCustomer'=> CustomerResolve::create(),
        'updateCustomer'=> CustomerResolve::update(),
        'deleteCustomer'=> CustomerResolve::delete(),

        'addContactToUser'=> ContactResolve::updateContact(),
        'updateUserRoles' => UserResolve::updateRoles(),
        'createUser'=> UserResolve::create(),
        'updateUser'=> UserResolve::update(),
        'deleteUser'=> UserResolve::delete(),

        'createRole' => RoleResolve::create(),
        'updateRole' => RoleResolve::update(),
        'updateRoleRules' => RoleResolve::updateRules(),
        'deleteRole' => RoleResolve::delete(),

        'createRule' => RuleResolve::create(),
        'updateRule' => RuleResolve::update(),
        'deleteRule' => RuleResolve::delete(),
    ]
];