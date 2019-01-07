<?php
namespace entities;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Doctrine\ORM\PersistentCollection;

/**
* @Entity @Table(name="user")
*/
class User extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
     * @OneToOne(targetEntity="Person", inversedBy="user")
     * @JoinColumn(name="person_id", referencedColumnName="id")
     */
	protected $person;

    /**
     * @Column(type="string", length=30)
     */
    protected $login;

    /**
     * @Column(type="string")
     */
    private $password;

    /**
     * @ManyToMany(targetEntity="Role")
     * @JoinTable(name="users_roles",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    protected $roles;

    /**
     * initial rewrite values for ProtoForGraph->getGraphArray()
     */
    public function initialRenamedArray()
    {
        $this->renamedKeys = [
            'roles' => $this->mapEntityCollectionToGraph($this->roles),
            'person' => function () {
                return [
                    'uuid' => $this->person->getUUID(),
                    'contacts' => $this->mapEntityCollectionToGraph($this->person->getContacts()),
                    'tags' => $this->mapEntityCollectionToGraph($this->person->getTags()),
                    'name' => $this->person->getName()
                ];
            }
        ];
    }

    public function __construct () {

        $this->roles = new ArrayCollection();
    }

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
    public function getPerson(): Person
    {
        return $this->person;
    }

    /**
     * @param mixed $person
     */
    public function setPerson($person)
    {
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function checkPassword($checkPass)
    {
        return password_verify($checkPass, $this->password);
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $options = [
            'cost' => 12,
        ];

        $this->password = password_hash($password, PASSWORD_BCRYPT, $options);
    }

}