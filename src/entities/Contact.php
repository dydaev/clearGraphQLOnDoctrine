<?php
namespace entities;
require __DIR__ . '/../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;
/**
* @Entity @Table(name="contact")
*/
class Contact extends ProtoForGraph
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
     * @ManyToOne(targetEntity="Person", inversedBy="contacts")
     * @JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @ManyToOne (targetEntity="ContactType")
     * @JoinColumn (name="type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @Column(type="string")
     */
    protected $value;

    public function __construct()
    {
         $this->uuid = Uuid::uuid4();
    }

    /**
     * initial re-write values for ProtoForGraph->getArray()
     */
    public function initialRenamedArray()
    {
        $this->renamedKeys = [
            'person' => null,
            'id' => null,
            'uuid' => function(){return ['uuid' => $this->getUUID()];},
            'value' => $this->value,
            'type' => $this->type->getGraphArray()
        ];
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
    public function getType()
    {
        return $this->type;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

//    public function getGraphContact() {
//
//    }

}