<?php
///**
// * Created by PhpStorm.
// * User: che
// * Date: 26.12.18
// * Time: 22:57
// */
//
//namespace tests\Resolvers\Types;
//
//require_once __DIR__.'/../../../vendor/autoload.php';
//
//use entities\Contact;
//use entities\Person;
//use entities\Tag;
//use Resolvers\Types\PersonResolve;
//use PHPUnit\Framework\TestCase;
//
//class PersonResolveTest extends TestCase
//{
//    private static $EM;
//    private static $testPersonUuid;
//
//    public function __construct(?string $name = null, array $data = [], string $dataName = '')
//    {
//        self::getEM();
//        parent::__construct($name, $data, $dataName);
//    }
//
//    private static function getEM() {
//        if(empty(self::$EM )) self::$EM = require_once __DIR__.'/../../../config/bootstrap.php';
//    }
//
//    public function testCreatePerson()
//    {
//        self::getEM();
//        $args = [
//            'name' => 'Test Pers'
//        ];
//        $res = PersonResolve::entityNew(self::$EM, $args);
//
//        self::$testPersonUuid = $res->getUUID();
//
//        echo('Created person with uuid: '. self::$testPersonUuid . '/n');
//
//        $this->assertEquals($args['name'], $res->getName());
//    }
//
//    public function testPersonUpdateName()
//    {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'name' => 'Test Person'
//        ];
//        $res = PersonResolve::entityUpdate(self::$EM, $args);
//
//        $this->assertEquals($args['name'], $res->getName());
//    }
//
//    public function testPersonAddTag()
//    {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'spirits',
//                    'color' => 'blue'
//                ]
//            ]
//        ];
//        $res = PersonResolve::entityUpdate(self::$EM, $args);
//
//        $res = $res->getTags()->map(function(Tag $tag){
//            return [
//                'name' => $tag->getName(),
//                'color' => $tag->getColor()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['tags'],$res);
//    }
//    public function testPersonUpdateTag()
//    {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'spirits',
//                    'color' => 'black'
//                ],
//                [
//                    'name' => 'Forg',
//                    'color' => 'green'
//                ]
//            ]
//        ];
//        $res = PersonResolve::entityUpdate(self::$EM, $args);
//
//        $res = $res->getTags()->map(function(Tag $tag){
//            return [
//                'name' => $tag->getName(),
//                'color' => $tag->getColor()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['tags'],$res);
//    }
//    public function testPersonAddNewTagUpdateOld()
//    {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'spirits',
//                    'color' => 'blue'
//                ],
//                [
//                    'name' => 'mood',
//                    'color' => 'black'
//                ],
//                [
//                    'name' => 'tazik',
//                    'color' => 'silver'
//                ]
//            ]
//        ];
//        $pers = PersonResolve::entityUpdate(self::$EM, $args);
//
//        $res = [];
//        $pers->getTags()->map(function(Tag $tag) use (&$res){
//            array_push($res, [
//                'name' => $tag->getName(),
//                'color' => $tag->getColor()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['tags'],$res);
//    }
//    public function testPersonDeleteNewTag()
//    {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'spirits',
//                    'color' => 'blue'
//                ],
//                [
//                    'name' => 'tazik',
//                    'color' => 'silver'
//                ]
//            ]
//        ];
//        $pers = PersonResolve::entityUpdate(self::$EM, $args);
//
//        $res = [];
//        $pers->getTags()->map(function(Tag $tag) use (&$res){
//            array_push($res, [
//                'name' => $tag->getName(),
//                'color' => $tag->getColor()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['tags'],$res);
//    }
//
//    public function testPersonAddNewContact() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'contacts' => [
//                [
//                    'value' => 'Jora',
//                    'typeId'=> 9
//                ]
//            ]
//        ];
//
//        PersonResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = $pers->getContacts()->map(function(Contact $contact){
//            return [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//    }
//
//    public function testPersonAddNewContact2() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'contacts' => [
//                [
//                    'value' => 'Jora',
//                    'typeId'=> 9
//                ],
//                [
//                    'value' => 'TestFaceb',
//                    'typeId'=> 7
//                ]
//            ]
//        ];
//
//        PersonResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = $pers->getContacts()->map(function(Contact $contact){
//            return [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//    }
//
//    public function testPersonUpdateNewContact() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'contacts' => [
//                [
//                    'value' => 'Jora',
//                    'typeId'=> 9
//                ],
//                [
//                    'value' => 'TestFacebook',
//                    'typeId'=> 7
//                ]
//            ]
//        ];
//
//        PersonResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = $pers->getContacts()->map(function(Contact $contact){
//            return [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//    }
//
//    public function testPersonDeleteFirstContact() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'contacts' => [
//                [
//                    'value' => 'TestFacebook',
//                    'typeId'=> 7
//                ]
//            ]
//        ];
//
//        PersonResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = $pers->getContacts()->map(function(Contact $contact){
//            return [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ];
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//    }
//
//    public function testDeletePerson() {
//
//        $args['uuid'] = self::$testPersonUuid;
//
//        $deletingPers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $deletedPers = PersonResolve::entityDelete(self::$EM, $args);
//
//        echo('deleted person '. $deletedPers->getName());
//
//        $this->assertEquals($deletingPers->getName(), $deletedPers->getName());
//    }
//}
