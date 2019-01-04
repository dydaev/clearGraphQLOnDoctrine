<?php
namespace entities;

use \Doctrine\Common\Collections\ArrayCollection as ArrayCollection;

/**
* @Entity @Table(name="role")
*/
class Role extends ProtoForGraph
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
    protected $name;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ManyToMany(targetEntity="Rule")
     * @JoinTable(name="roles_rules",
     *      joinColumns={@JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="rule_id", referencedColumnName="id")}
     *      )
     */
    protected $rulesList;

   	public function __construct () {
        $this->rulesList = new ArrayCollection();
    }

    /**
     * initial rewrite values for ProtoForGraph->getGraphArray()
     */
    public function initialRenamedArray()
    {
        $this->renamedKeys = [
            '$rulesList' => function () {
                return [
                    'rules' => $this->mapEntityCollectionToGraph($this->getRules()),
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
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getRules()
    {
        return $this->rulesList;
    }

    /**
     * @param Rule $rule
     */
    public function addRule(Rule $rule)
    {
        $this->rulesList->add($rule);
    }

    public function clearRules()
    {
        $this->rulesList->clear();
    }

    /**
     * @param Rule $rule
     */
    public function removeAccess(Rule $rule)
    {
        $this->rulesList->removeElement($rule);
    }

    /**
     * @param mixed $rulesList
     */
    public function setAccessList($rulesList)
    {
        $this->rulesList = $rulesList;
    }

    /**
     * @param String $name
     */
    public function setName($name)
    {
        $this->name = $name;
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