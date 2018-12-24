<?php
namespace entities;

/**
* @Entity @Table(name="tag")
*/
class Tag
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Column(type="string", length=50, unique=true)
     */
    private $name;

    /**
     * @Column(type="string", length=25, nullable=true)
     */
    private $color;

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

}