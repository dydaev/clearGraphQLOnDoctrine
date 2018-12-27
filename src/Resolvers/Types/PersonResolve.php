<?php
namespace Resolvers\Types;

require_once __DIR__.'/../../../vendor/autoload.php';

use entities\Tag;
use GraphQL\Error\Error;
use \Doctrine\ORM\EntityManager;
use \entities\Person;
use \entities\Contact;
use \entities\Customer;

class PersonResolve
{
    /**
     *  @params $EM object Entity Manager
     *  @params $args array arguments for person(name,contacts,tags...etc)
     *  @return Person entity
     */
    public static function entityNew($EM, $args): \entities\Person
    {
      $person = new Person();

      if(!empty($args['name'])) $person->setName();

      if (is_array($args['contacts']) && count($args['contacts'] > 0)) {
        foreach ($args['contacts'] as $contact) {

            $newContact = ContactResolve::entityNew($EM, $contact);

            $person->addContact($newContact);
        }
      }

      if (is_array($args['tags']) && count($args['tags'] > 0)) {
          foreach ($args['tags'] as $tag) {
            
              $newTag = TagResolve::entityNew($EM, $tag);
              
              $person->addTag($newTag);
          }
      }

      $EM->persist($person);
      $EM->flush();

      return $person;
  }
    /**
     * $EM : Entity Manager
     * $args : arguments for person entityes (name, tags, contacts ...etc)
     */
    public static function entityUpdate($EM, $args): \entities\Person
    {
        if (!empty($args['uuid'])) {

            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {

                $isChangedPerson = false;

                if(!empty($args['name']) && $person->getName() !== $args['name']) {

                    $person->setName($args['name']);
                    $isChangedPerson = true;
                }

                if(!empty($args['tags'])) {

                    $updatingTags = $args['tags'];

                    if (count($updatingTags) > 0) {

                        // removing $person tags not contained in the $updatingTags
                        //
                        $person->getTags()->map(function (Tag $tag) use ($updatingTags, $person, &$isChangedPerson) {

                            if (!current(array_filter($updatingTags, function ($updatingTag) use ($tag) {
                                return (!empty($updatingTag['name']) && $tag->getName() === $updatingTag['name']);
                            }))) {
                                $person->getTags()->removeElement($tag);
                                $isChangedPerson = true;

                            }

                        });

                        // adding and updating tags contained in the $updatingTags to $person
                        //
                        foreach ($updatingTags as $updatingTag) {

                            if (!empty($updatingTag['name'])) {

                                $tagFromDB = $EM->getRepository('entities\Tag')->findOneBy(['name' => $updatingTag['name']]);

                                if (empty($tagFromDB))
                                    $tagFromDB = TagResolve::entityNew($EM, $updatingTag);
                                else
                                    $tagFromDB = TagResolve::entityUpdate($EM, $updatingTag);

                                if (!empty($tagFromDB) && $person->addTag($tagFromDB)) $isChangedPerson = true;

                            }
                        }

                    } else $isChangedPerson = $person->removeAllTags();
                }

                if(!empty($args['contacts'])) {

                    $updatingContacts = $args['contacts'];

                    if (count($updatingContacts) > 0) {

                        // removing $person contacts not contained in the $updatingContacts
                        //
                        $person->getContacts()->map(function (Contact $contact) use ($updatingContacts, $person, &$isChangedPerson) {

                            if (!current(array_filter($updatingContacts, function ($updatingContact) use ($contact) {
                                return (!empty($updatingContact['uuid']) && $contact->getUUID() === $updatingContact['uuid']);
                            }))) {

                                $person->getContacts()->removeElement($contact);
                                $isChangedPerson = true;
                            }

                        });

                        // adding and updating tags contained in the $updatingContacts to $person
                        //
                        foreach ($updatingContacts as $updatingContact) {

                            $contactFromDB = [];

                            switch (true) {
                                case (!empty($updatingContact['uuid'])):
                                    $contactFromDB = $EM->getRepository('entities\Contact')->findOneBy(['uuid' => $updatingContact['uuid']]);

                                    if (!empty($contactFromDB)) {

                                        $contactFromDB = ContactResolve::entityUpdate($EM, $updatingContact);
                                        break;
                                    }
                                case (!empty($updatingContact['value']) && !empty($updatingContact['typeId'])):

                                    $contactFromDB = ContactResolve::entityNew($EM, $updatingContact);

                            }

                            if (!empty($contactFromDB) && $person->addContact($contactFromDB)) $isChangedPerson = true;

                        }

                    } else $isChangedPerson = $person->removeAllTags();
                }


                if ($isChangedPerson){

                    $EM->persist($person);

                    $EM->flush();
                }

                return $person;
            }// else throw new Error('contact for updating is not found');
        }
        return null;
    }

}