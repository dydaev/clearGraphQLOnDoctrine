<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 20.12.18
 * Time: 20:17
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__."/../vendor/autoload.php";

class DoctrineManager
{
    private static $conn;
    private static $isDevMode;
    private static $config;
    private static $entityManager;


    public static function getEntityManager() {
        if (self::$entityManager) return self::$entityManager;

         self::$conn = array(
            'dbname' => 'clear_graph',
            'user' => 'orm_use',
            'password' => 'orm',
            'host' => 'localhost',
            'driver' => 'pdo_mysql'
        );
        self::$isDevMode = true;
        self::$config = Setup::createAnnotationMetadataConfiguration(array("src/entities"), self::$isDevMode);
        self::$entityManager = EntityManager::create(self::$conn, self::$config);

        return self::$entityManager;
    }

}