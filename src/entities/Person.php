<?php
namespace entities;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
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
    public function getTags()
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
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getContacts()
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
     * Add contacts
     *
     * @param (Contact $contactArray
     */
    public function addContact(Contact $contact)
    {

        $this->contacts->add($contact);
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

}
