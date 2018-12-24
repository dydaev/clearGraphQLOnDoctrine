<?php
namespace entities;

/**
* @Entity @Table(name="contact_type")
*/
class ContactType extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", unique=true)
     */
    protected $type;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $regex;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $prefix;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getRegEx()
    {
        return $this->regex;
    }

    /**
     * @param mixed $regex
     */
    public function setRegEx($regex)
    {
        $this->regex = $regex;
    }

}