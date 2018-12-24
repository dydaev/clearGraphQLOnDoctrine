<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 15.12.18
 * Time: 16:54
 */

require_once "bootstrap.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
