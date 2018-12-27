<?php
namespace entities;

/**
* @Entity @Table(name="tag")
*/
class Tag extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column(type="string", length=50, unique=true)
     */
    protected $name;

    /**
     * @Column(type="string", length=25, nullable=true)
     */
    protected $color;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $text
     */
    public function setName($text)
    {
        $this->name = $text;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    //TODO : do tag manyToMany

}