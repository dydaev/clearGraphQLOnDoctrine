<?php
namespace entities;
/**
* @Entity @Table(name="customer")
*/
class Customer extends ProtoForGraph
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="Person", inversedBy="customer")
     * @JoinColumn(name="person_id", referencedColumnName="id")
     */
    protected $person;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $discount_card;

    /**
     * initial rewrite values for ProtoForGraph->getGraphArray()
     */
    public function initialRenamedArray()
    {
        $this->renamedKeys = [
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
    public function setPerson(Person $person)
    {
        $person->setCustomer($this);
        $this->person = $person;
    }

    /**
     * @return mixed
     */
    public function getDiscount_card()
    {
        return $this->discount_card;
    }

    /**
     * @param mixed $discount_card
     */
    public function setDiscount_card($discount_card)
    {
        $this->discount_card = $discount_card;
    }

}