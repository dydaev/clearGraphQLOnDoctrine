<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__.'/../config/bootstrap.php';
require_once __DIR__.'/../config/DoctrineManager.php';


use Siler\Graphql;
use Siler\Http\Request;
use Siler\Http\Response;

Response\header('Access-Control-Allow-Origin', '*');
Response\header('Access-Control-Allow-Headers', 'content-type');

$context = [
    'EntityManager' => DoctrineManager::getEntityManager()
];

if (Request\method_is('post')) {
    $schema = include __DIR__.'/schema.php';
    Graphql\init($schema, null, $context);
}