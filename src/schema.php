<?php
require_once __DIR__.'/../vendor/autoload.php';

use Siler\Graphql;

$typeDefs = file_get_contents(__DIR__ . '/Schemas/schema.graphql');
$resolvers = include __DIR__.'/Resolvers/resolvers.php';

return Graphql\schema($typeDefs, $resolvers);