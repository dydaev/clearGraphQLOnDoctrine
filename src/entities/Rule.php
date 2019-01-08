<?php
namespace entities;

/**
* @Entity @Table(name="rule")
*/
class Rule extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string")
     */
    protected $rulePath;

    /**
    * @Column(type="integer", length=3)
    */
    protected $permission;

    /**
     * @Column(type="string", length=1000, nullable=true)
     */
    protected $description;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getRulePath(): String
    {
        return $this->rulePath;
    }

    /**
     * @param string $rulePath
     */
    public function setRulePath(String $rulePath)
    {
        $this->rulePath = $rulePath;
    }

    /**
     * @return integer
     */
    public function getPermission(): Int
    {
        return $this->permission;
    }

    /**
     * @param integer $permission
     */
    public function setPermission(Int $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @return string
     */
    public function getDescription(): String
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(String $description)
    {
        $this->description = $description;
    }


}