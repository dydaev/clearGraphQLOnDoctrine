<?php
/**
 * Created by PhpStorm.
 * User: che
 * Date: 15.12.18
 * Time: 22:43
 */
require __DIR__ . '/../vendor/autoload.php';
require_once  __DIR__ . '/../config/bootstrap.php';
// use entities\Person;
// use entities\Customer;
use entities\ContactType;
use entities\Contact;
// use \Ramsey\Uuid\Uuid;

// $pers = new Person();
//$contT = new ContactType();

//$contT->setType('Facebook');
//$rus = $entityManager->getRepository('\entities\ContactType')->findOneBy(['type' => 'facebook']);
echo('<pre>');
// print_r(ContactsType::getContactsType($rus));
// print_r($rus->getArray());
echo('</pre>');
//$entityManager->persist($contT);
//$entityManager->flush();
// $pers->setUid(Uuid::uuid4());
// $pers->setName('Sarah');

// $entityManager->persist($pers);
// $entityManager->flush();
// $res = $entityManager->getRepository('entities\Customer')->findAll();//getRepository('entities\Customer')->find(1);
$res = $entityManager->getRepository('entities\Tag')->findAll();


 echo('<pre>');

 print_r($res);
echo('</pre>');
/*
 $res = $entityManager->getRepository('entities\Customer')->find(1);
 $type = $entityManager->getRepository('entities\ContactType')->findOneBy(['type' => 'facebook']);

 $pers = $res->getPerson();

 $contact = new Contact();
 $contact->setType($type);
 $contact->setPerson($pers);
 $contact->setValue('https://www.facebook.com/FilipKirkorovBulgaria');

 $entityManager->persist($contact);
$entityManager->flush();*/

//Головко в 16-20
//Чавдар-38б
//
//1порт патчкорд один висит и синяя СФП
//
//
//Свитч 12 обычная


// $pers->addContact($contact);

//$entityManager->persist($pers);

// $res = array_map(function($pers){
//     return $pers->getPerson()->getName();
// }, $res);
echo('<pre>');
// print_r(ContactsType::getContactsType($contT));
//  var_dump($res->getArray());
  print_r(array_map(function($contact){return $contact->getGraphArray();},$res));
echo('</pre>');
//
//foreach ($pers as $per) {
//    echo($per->getName());
//}


// $cust = new Customer();

// $cust->setPerson($pers);
// $cust->setDiscount_card('9244673');

// $entityManager->persist($cust);
// $entityManager->flush();
//
//echo($cust->getPerson()->getName());
//var_dump($cust);