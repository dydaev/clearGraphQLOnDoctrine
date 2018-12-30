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
     * @param EntityManager $EM
     * @param mixed $args array arguments for person(name,contacts,tags...etc)
     * @return Person entity
     */
    public static function entityNew(EntityManager $EM, $args): Person
    {
      $person = new Person();

      if(!empty($args['name'])) $person->setName($args['name']);

      if (!empty($args['contacts']) && is_array($args['contacts']) && count($args['contacts']) > 0) {

        foreach ($args['contacts'] as $contact) {

            if (empty($contact['uuid']))

                $contactForPerson = ContactResolve::entityNew($EM, $contact);
            else
                $contactForPerson = ContactResolve::entityUpdate($EM, $contact);

            if(!empty($contactForPerson)) {

                $person->addContact($contactForPerson);
                $EM->persist($person);

            } else throw new Error('contact is empty and did not add to person');

        }
      }

      if (!empty($args['tags']) && is_array($args['tags']) && count($args['tags']) > 0) {
          foreach ($args['tags'] as $tag) {

              $tag = TagResolve::entityNew($EM, $tag);

              if (!empty($tag)) {

                  $person->addTag($tag);
                  $EM->persist($person);

              } else throw new Error('tag is empty and did not add to person');

          }
      }

      $EM->persist($person);
      $EM->flush();

      return $person;
  }
    /**
     *@param EntityManager $EM
     *@param mixed $args : arguments for person entityes (name, tags, contacts ...etc)
     *@return Person | null
     */
    public static function entityUpdate(EntityManager $EM, $args): Person
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

        /**
     *@param EntityManager $EM
     *@param mixed $args : arguments for person entityes (name, tags, contacts ...etc)
     *@return Person | null
     */
    public static function entityDelete(EntityManager $EM, $args): Person
    {
        if(!empty($args['uuid'])) {
            $person = $EM->getRepository('entities\Person')->findOneBy([ 'uuid' => $args['uuid'] ]);

            if (!empty($person)) {
                $EM->remove($person);
                $EM->flush();

                return $person;
            }
        }
    }

}