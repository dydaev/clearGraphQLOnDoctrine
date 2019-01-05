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
    private $id;

    /**
     * @Column(type="string")
     */
    private $rulePath;

    /**
    * @Column(type="integer", length=3)
    */
    private $permission;

    /**
     * @Column(type="string", length=25, nullable=true)
     */
    private $description;

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