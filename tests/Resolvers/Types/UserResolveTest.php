<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 26.12.18
 * Time: 22:57
 */

namespace tests\Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

use entities\Contact;

use entities\Person;
use entities\Tag;
use Resolvers\Types\CustomerResolve;
use Resolvers\Types\PersonResolve;
use PHPUnit\Framework\TestCase;
use Resolvers\Types\UserResolve;

class UserResolveTest extends TestCase
{
    private static $EM;
    private static $testPersonUuid;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        if(empty(self::$EM )) self::$EM = require_once __DIR__.'/../../../config/bootstrap.php';
        parent::__construct($name, $data, $dataName);
    }

    public function testCreateUser()
    {
        $name = 'Test User';
        $login = 'Fedya';
        $pass = '123';

        $pers = new Person();

        $user = UserResolve::entityNew(self::$EM, $pers, $login, $pass, $name);

        self::$testPersonUuid = $user->getPerson()->getUUID();

        $this->assertEquals($name, $user->getPerson()->getName());
    }
//
//    public function testCustomerAddFirstContacts() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'contacts' => [
//                [
//                    'value' => 'TestFacebook',
//                    'typeId'=> 7
//                ],
//                [
//                    'value' => 'TestInstagram',
//                    'typeId' => 9
//                ]
//            ]
//        ];
//
//        CustomerResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = [];
//        $pers->getContacts()->map(function(Contact $contact) use (&$res){
//            array_push($res, [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//    }
//
//    public function testCustomerDeleteSecondContact() {
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
//        CustomerResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = [];
//        $pers->getContacts()->map(function(Contact $contact) use (&$res){
//            array_push($res, [
//                'value' => $contact->getValue(),
//                'typeId' => $contact->getType()->getId()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['contacts'], $res);
//
//    }
//
//    public function testCustomerAddFirstTags() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'Test1',
//                    'color' => null
//                ],
//                [
//                    'name' => 'Test2',
//                    'color' => 'pink'
//                ]
//            ]
//        ];
//
//        CustomerResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = [];
//        $pers->getTags()->map(function(Tag $contact) use (&$res){
//            array_push($res, [
//                'name' => $contact->getName(),
//                'color' => $contact->getColor()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['tags'], $res);
//    }
//    public function testCustomerDeleteFirstTag() {
//        $args = [
//            'uuid' => self::$testPersonUuid,
//            'tags' => [
//                [
//                    'name' => 'Test2',
//                    'color' => 'pink'
//                ]
//            ]
//        ];
//
//        CustomerResolve::entityUpdate(self::$EM, $args);
//
//        $pers = self::$EM->getRepository('entities\Person')->findOneBy(['uuid' => $args['uuid']]);
//
//        $res = [];
//        $pers->getTags()->map(function(Tag $contact) use (&$res){
//            array_push($res, [
//                'name' => $contact->getName(),
//                'color' => $contact->getColor()
//            ]);
//        })->toArray();
//
//        $this->assertEquals($args['tags'], $res);
//    }
//
    public function testDeleteUser() {
        $name = 'Test User';

        $pers = self::$EM->getRepository('entities\Person')->findOneBy([ 'uuid' => self::$testPersonUuid ]);

        $cust = UserResolve::entityDelete(self::$EM, $pers);


        $this->assertEquals($name, $cust->getPerson()->getName());
    }
}
