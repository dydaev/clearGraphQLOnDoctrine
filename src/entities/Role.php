<?php
namespace entities;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
* @Entity @Table(name="role")
*/
class Role
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", unique=true)
     */
    private $role;

    /**
     * @Column(type="string", nullable=true)
     */
    private $description;

    /**
     * @ManyToMany(targetEntity="AccessRolesList")
     * @JoinTable(name="role_access",
     *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="access_id", referencedColumnName="id")}
     *      )
     */
    private $accessList;

   	public function __construct () {
        $this->accessList = new ArrayCollection();
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
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getAccessList()
    {
        return $this->accessList;
    }

    /**
     * @param mixed $accessList
     */
    public function setAccessList($accessList)
    {
        $this->accessList = $accessList;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }


}