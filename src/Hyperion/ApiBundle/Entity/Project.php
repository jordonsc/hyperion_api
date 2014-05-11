<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="projects")
 */
class Project implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $bake_status;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $baked_image_id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="projects")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;

    /**
     * @ORM\OneToMany(targetEntity="Distribution", mappedBy="project")
     */
    protected $distributions;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="project")
     */
    protected $actions;

    /**
     * @ORM\OneToOne(targetEntity="Schema", inversedBy="project")
     */
    protected $schema;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="prod_projects")
     * @ORM\JoinColumn(name="prod_credential_id", referencedColumnName="id")
     */
    protected $prod_credential;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="test_projects")
     * @ORM\JoinColumn(name="test_credential_id", referencedColumnName="id")
     */
    protected $test_credential;

    // --

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->distributions = new ArrayCollection();
        $this->actions = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }


    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add distributions
     *
     * @param Distribution $distributions
     * @return Project
     */
    public function addDistribution(Distribution $distributions)
    {
        $this->distributions[] = $distributions;

        return $this;
    }

    /**
     * Remove distributions
     *
     * @param Distribution $distributions
     */
    public function removeDistribution(Distribution $distributions)
    {
        $this->distributions->removeElement($distributions);
    }

    /**
     * Get distributions
     *
     * @return Collection
     */
    public function getDistributions()
    {
        return $this->distributions;
    }

    /**
     * Add actions
     *
     * @param Action $actions
     * @return Project
     */
    public function addAction(Action $actions)
    {
        $this->actions[] = $actions;

        return $this;
    }

    /**
     * Remove actions
     *
     * @param Action $actions
     */
    public function removeAction(Action $actions)
    {
        $this->actions->removeElement($actions);
    }
    /**
     * Get actions
     *
     * @return Collection
     */
    public function getActions()
    {
        return $this->actions;
    }


    /**
     * Set account
     *
     * @param Account $account
     * @return Project
     */
    public function setAccount(Account $account = null)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * Get account
     *
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set schema
     *
     * @param Schema $schema
     * @return Project
     */
    public function setSchema(Schema $schema = null)
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * Get schema
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Set prod_credential
     *
     * @param Credential $prodCredential
     * @return Project
     */
    public function setProdCredential(Credential $prodCredential = null)
    {
        $this->prod_credential = $prodCredential;

        return $this;
    }

    /**
     * Get prod_credential
     *
     * @return Credential
     */
    public function getProdCredential()
    {
        return $this->prod_credential;
    }

    /**
     * Set test_credential
     *
     * @param Credential $testCredential
     * @return Project
     */
    public function setTestCredential(Credential $testCredential = null)
    {
        $this->test_credential = $testCredential;

        return $this;
    }

    /**
     * Get test_credential
     *
     * @return Credential
     */
    public function getTestCredential()
    {
        return $this->test_credential;
    }

    /**
     * Set bake_status
     *
     * @param integer $bakeStatus
     * @return Project
     */
    public function setBakeStatus($bakeStatus)
    {
        $this->bake_status = $bakeStatus;

        return $this;
    }

    /**
     * Get bake_status
     *
     * @return integer 
     */
    public function getBakeStatus()
    {
        return $this->bake_status;
    }

    /**
     * Set baked_image_id
     *
     * @param string $bakedImageId
     * @return Project
     */
    public function setBakedImageId($bakedImageId)
    {
        $this->baked_image_id = $bakedImageId;

        return $this;
    }

    /**
     * Get baked_image_id
     *
     * @return string 
     */
    public function getBakedImageId()
    {
        return $this->baked_image_id;
    }
}
