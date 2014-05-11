<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="credentials")
 */
class Credential implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Account", inversedBy="credentials")
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id")
     */
    protected $account;

    /**
     * @ORM\Column(type="integer")
     */
    protected $provider;

    /**
     * @ORM\Column(type="string")
     */
    protected $key;

    /**
     * @ORM\Column(type="string")
     */
    protected $secret;

    /**
     * @ORM\Column(type="string")
     */
    protected $region;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="prod_credential")
     */
    protected $prod_projects;

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="test_credential")
     */
    protected $test_projects;

    // --


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->prod_projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->test_projects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set provider
     *
     * @param integer $provider
     * @return Credential
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * Get provider
     *
     * @return integer
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return Credential
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return Credential
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return Credential
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set account
     *
     * @param Account $account
     * @return Credential
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
     * Add prod_projects
     *
     * @param Project $prodProjects
     * @return Credential
     */
    public function addProdProject(Project $prodProjects)
    {
        $this->prod_projects[] = $prodProjects;

        return $this;
    }

    /**
     * Remove prod_projects
     *
     * @param Project $prodProjects
     */
    public function removeProdProject(Project $prodProjects)
    {
        $this->prod_projects->removeElement($prodProjects);
    }

    /**
     * Get prod_projects
     *
     * @return Collection
     */
    public function getProdProjects()
    {
        return $this->prod_projects;
    }

    /**
     * Add test_projects
     *
     * @param Project $testProjects
     * @return Credential
     */
    public function addTestProject(Project $testProjects)
    {
        $this->test_projects[] = $testProjects;

        return $this;
    }

    /**
     * Remove test_projects
     *
     * @param Project $testProjects
     */
    public function removeTestProject(Project $testProjects)
    {
        $this->test_projects->removeElement($testProjects);
    }

    /**
     * Get test_projects
     *
     * @return Collection
     */
    public function getTestProjects()
    {
        return $this->test_projects;
    }
}
