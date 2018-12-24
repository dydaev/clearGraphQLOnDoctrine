<?php
namespace entities;

/**
* @Entity @Table(name="accessRoleList")
*/
class AccessRolesList
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
    private $accessRightPath;

    /**
    * @Column(type="integer", length=3)
    */
    private $permission;

    /**
     * @Column(type="string", length=25, nullable=true)
     */
    private $description;

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
    public function getAccessRightPath()
    {
        return $this->accessRightPath;
    }

    /**
     * @param mixed $accessRightPath
     */
    public function setAccessRightPath($accessRightPath)
    {
        $this->accessRightPath = $accessRightPath;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
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