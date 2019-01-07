<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/bootstrap.php';
require_once __DIR__.'/../config/DoctrineManager.php';

session_id('cli');//TODO comment before prod
session_start();

use Siler\Graphql;
use Siler\Http\Request;
use Siler\Http\Response;
use DoctrineManager;
use Utils\Utils;

date_default_timezone_set('UTC');

Response\header('Access-Control-Allow-Origin', '*');
Response\header('Access-Control-Allow-Headers', 'content-type');

$token = Request\header('Token');

$context = [
    'EntityManager' => DoctrineManager::getEntityManager(),
    'user' => $token !== "null" ? Utils::getMySelf($token) : null
];

if (Request\method_is('post')) {

    $schema = include __DIR__.'/schema.php';
    Graphql\init($schema, null, $context);

}

