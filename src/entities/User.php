<?php
namespace entities;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPerson()
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
        return $this->password === md5($checkPass);
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = md5($password);
    }

}