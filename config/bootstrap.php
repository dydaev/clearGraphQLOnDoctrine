<?php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Doctrine\DBAL\Types\Type;

require_once __DIR__."/../vendor/autoload.php";

Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$config = Setup::createAnnotationMetadataConfiguration(array("src/entities"), $isDevMode);

// database configuration parameters
$conn = array(
    'dbname' => 'clear_graph',
    'user' => 'orm_use',
    'password' => 'orm',
    'host' => 'localhost',
    'driver' => 'pdo_mysql'
);



// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
return $entityManager;
