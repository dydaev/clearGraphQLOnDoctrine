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
      $customer = new Customer;

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
//            $person = new Person();

            if (!empty($person)) {

                $isChangedPerson = false;

                if(!empty($args['name']) && $person->getName() !== $args['name']) {

                    $person->setName($args['name']);
                    $isChangedPerson = true;
                }

                $updatingTags = $args['tags'];

                if(count($updatingTags) > 0) {

                    // removing $person tags not contained in the $updatingTags
                    //
                    $person->getTags()->map(function(Tag $tag) use ($updatingTags, $person) {

                        if (!current(array_filter($updatingTags, function($updatingTag) use ($tag) {
                            return $tag->getName() === $updatingTag['name'];
                        })))
                            $person->getTags()->removeElement($tag);

                    });

                    // adding and updating tags contained in the $updatingTags to $person
                    //
                    foreach ($updatingTags as $updatingTag) {

                        if (!empty($updatingTag['name'])) {
                            $tagFromDB = $EM->getRepository('entities\Tag')->findOneBy([ 'name' => $updatingTag['name'] ]);

                            if (empty($tagFromDB))
                                $tagFromDB = TagResolve::entityNew($EM, $updatingTag);
                            else
                                $tagFromDB = TagResolve::entityUpdate($EM, $updatingTag);

                            if (!empty($tagFromDB)) $isChangedPerson = $person->addTag($tagFromDB);
                        }
                    }

                } else $isChangedPerson = $person->removeAllTags();

                //TODO : updating contacts

                if ($isChangedPerson){

                    if ($isChangedPerson) $EM->persist($person);

                    $EM->flush();
                }

                return $person;
            } else throw new Error('contact for updating is not found');
        }
        return null;
    }

}