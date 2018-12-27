<?php
namespace entities;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/../../vendor/autoload.php';
/**
* @Entity @Table(name="person")
*/
class Person extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=36, unique=true)
    **/
    protected $uuid;

    /**
     * @Column(type="string")
     */
    protected $name;

    /**
    * @OneToOne (targetEntity="User", mappedBy="person")
    */
    protected $user;

    /**
    * @OneToOne (targetEntity="Customer", mappedBy="person")
    */
    protected $customer;

    /**
    *@OneToMany(targetEntity="Contact", mappedBy="person", cascade={"all"}, orphanRemoval=true )
    */
    protected $contacts;

     /**
     * @ManyToMany(targetEntity="Tag")
     * @JoinTable(name="persons_tag",
     *      joinColumns={@JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    protected $tags;

     /**
     * @ManyToMany(targetEntity="Role")
     * @JoinTable(name="persons_role",
     *      joinColumns={@JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    protected $roles;

	public function __construct () {
	    $this->uuid = Uuid::uuid4();
        $this->contacts = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * initial re-write values for ProtoForGraph->getArray()
     */
    public function initialRenamedArray()
    {
        $this->renamedKeys = [
            'contacts' => function () {
            return ['ss'=> 'ss'];
//                return ['contacts' => $this->getContactsArray()];//$this->contacts->getArray();
            },
            'user' => null,
            'customer' => null,
            'roles' => 'roles',
            'id' => null,
            'uuid' => function(){return ['uuid' => $this->getUUID()];},
            'tags' => 'tags'

        ];
    }

    /**
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }
//    /**
//     * @param mixed $uid
//     */
//    public function setUUID($uid)
//    {
//        $this->uuid = $uid;
//    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getTags(): PersistentCollection
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param Tag $tag
     * @return boolean
     */
    public function addTag(Tag $tag): bool
    {
        if (!$this->hasTag($tag))
            return $this->tags->add($tag);

        return false;
    }

    /**
     * @return mixed
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer(Customer $customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getContacts(): PersistentCollection
    {
        return $this->contacts;
    }

    /**
     * @return mixed
     */
    public function getContactsArray()
    {
        return $this->contacts->toArray();
    }

     /**
     * Set contacts
     *
     * @param (Contact $contactArray
     */
    public function setContacts(Contact $contactArray)
    {
        $this->contacts = $contactArray;
    }
     /**
     * Add contacts
     *
     * @param (Contact $contactArray
     */
    public function addContacts(Contact $contactArray)
    {
        $this->contacts = $contactArray;
    }

    /**
     * @param Contact $contact
     * @return boolean
     */
    public function addContact(Contact $contact): bool
    {
        if (!$this->hasTag($contact))
            return $this->tags->add($contact);

        return false;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param Tag $tag
     * @return boolean
     */
    public function hasTag(Tag $tag):bool
    {
        return $this->tags->contains($tag);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function hasTagById($id): bool {
        return $this->tags->exists(function($key, Tag $personTag) use ($id){
            return $personTag->getId() === $id;
        });
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasTagByName($name): bool {
        return $this->tags->exists(function($key, Tag $personTag) use ($name){
            return $personTag->getName() === $name;
        });
    }

    /**
     * @param Contact $contact
     * @return boolean
     */
    public function hasContact(Contact $contact):bool
    {
        return $this->contacts->contains($contact);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function hasContactById($id): bool {
        return $this->contacts->exists(function($key, Contact $personTag) use ($id){
            return $personTag->getId() === $id;
        });
    }

    /**
     * @param string $value
     * @return bool
     */
    public function hasContactByValue($value): bool {
        return $this->tags->exists(function($key, Contact $personTag) use ($value){
            return $personTag->getValue() === $value;
        });
    }

    /**
     * @return bool
     */
    public function removeAllTags(): bool {
        $this->tags->clear(); //tags = new ArrayCollection();
        return true;
    }
}
