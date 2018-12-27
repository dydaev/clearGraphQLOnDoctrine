<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 26.12.18
 * Time: 22:57
 */

namespace tests\Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

use entities\Tag;
use Resolvers\Types\PersonResolve;
use PHPUnit\Framework\TestCase;

class PersonResolveTest extends TestCase
{
    private static $EM;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if(empty(self::$EM )) self::$EM = require_once __DIR__.'/../../../config/bootstrap.php';
        parent::__construct($name, $data, $dataName);
    }

    public function testPersonUpdateName()
    {
        $args = [
            'uuid' => 'e92d6f69-8dc4-46cd-a9e3-526753a71072',
            'name' => 'Filip Kirkoroff'
        ];
        $res = PersonResolve::entityUpdate(self::$EM, $args);

        $this->assertEquals($args['name'], $res->getName());
    }

    public function testPersonAddTag()
    {
        $args = [
            'uuid' => 'e92d6f69-8dc4-46cd-a9e3-526753a71072',
            'tags' => [
                [
                    'name' => 'spirits',
                    'color' => 'blue'
                ]
            ]
        ];
        $res = PersonResolve::entityUpdate(self::$EM, $args);

        $res = $res->getTags()->map(function(Tag $tag){
            return [
                'name' => $tag->getName(),
                'color' => $tag->getColor()
            ];
        })->toArray();

        $this->assertEquals($args['tags'],$res);
    }
    public function testPersonUpdateTag()
    {
        $args = [
            'uuid' => 'e92d6f69-8dc4-46cd-a9e3-526753a71072',
            'tags' => [
                [
                    'name' => 'spirits',
                    'color' => 'black'
                ]
            ]
        ];
        $res = PersonResolve::entityUpdate(self::$EM, $args);

        $res = $res->getTags()->map(function(Tag $tag){
            return [
                'name' => $tag->getName(),
                'color' => $tag->getColor()
            ];
        })->toArray();

        $this->assertEquals($args['tags'],$res);
    }
    public function testPersonAddNewTagUpdateOld()
    {
        $args = [
            'uuid' => 'e92d6f69-8dc4-46cd-a9e3-526753a71072',
            'tags' => [
                [
                    'name' => 'spirits',
                    'color' => 'blue'
                ],
                [
                    'name' => 'mood',
                    'color' => 'black'
                ]
            ]
        ];
        $res = PersonResolve::entityUpdate(self::$EM, $args);

        $res = $res->getTags()->map(function(Tag $tag){
            return [
                'name' => $tag->getName(),
                'color' => $tag->getColor()
            ];
        })->toArray();

        $this->assertEquals($args['tags'],$res);
    }
    public function testPersonDeleteNewTag()
    {
        $args = [
            'uuid' => 'e92d6f69-8dc4-46cd-a9e3-526753a71072',
            'tags' => [
                [
                    'name' => 'spirits',
                    'color' => 'blue'
                ]
            ]
        ];
        $res = PersonResolve::entityUpdate(self::$EM, $args);

        $res = $res->getTags()->map(function(Tag $tag){
            return [
                'name' => $tag->getName(),
                'color' => $tag->getColor()
            ];
        })->toArray();

        $this->assertEquals($args['tags'],$res);
    }
}
