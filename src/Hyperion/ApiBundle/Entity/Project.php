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
     * @ORM\Column(type="integer")
     */
    protected $account_id;

    /**
     * @ORM\OneToMany(targetEntity="Distribution", mappedBy="project")
     */
    protected $distributions;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="project")
     */
    protected $actions;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="prod_projects")
     * @ORM\JoinColumn(name="prod_credential_id", referencedColumnName="id")
     */
    protected $prod_credential;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $prod_credential_id;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="test_projects")
     * @ORM\JoinColumn(name="test_credential_id", referencedColumnName="id")
     */
    protected $test_credential;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $test_credential_id;

    /**
     * @ORM\Column(type="string")
     */
    protected $source_image_id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $packager;

    /**
     * @ORM\Column(type="integer")
     */
    protected $update_system_packages;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $packages;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $script;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $services;

    /**
     * @ORM\ManyToOne(targetEntity="Proxy")
     * @ORM\JoinColumn(name="prod_proxy_id", referencedColumnName="id")
     */
    protected $prod_proxy;

    /**
     * @ORM\ManyToOne(targetEntity="Proxy")
     * @ORM\JoinColumn(name="test_proxy_id", referencedColumnName="id")
     */
    protected $test_proxy;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $prod_proxy_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $test_proxy_id;

    // --


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
     * Constructor
     */
    public function __construct()
    {
        $this->distributions = new ArrayCollection();
        $this->actions = new ArrayCollection();
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

    /**
     * Set source_image_id
     *
     * @param string $sourceImageId
     * @return Project
     */
    public function setSourceImageId($sourceImageId)
    {
        $this->source_image_id = $sourceImageId;

        return $this;
    }

    /**
     * Get source_image_id
     *
     * @return string 
     */
    public function getSourceImageId()
    {
        return $this->source_image_id;
    }

    /**
     * Set packager
     *
     * @param integer $packager
     * @return Project
     */
    public function setPackager($packager)
    {
        $this->packager = $packager;

        return $this;
    }

    /**
     * Get packager
     *
     * @return integer 
     */
    public function getPackager()
    {
        return $this->packager;
    }

    /**
     * Set update_system_packages
     *
     * @param integer $updateSystemPackages
     * @return Project
     */
    public function setUpdateSystemPackages($updateSystemPackages)
    {
        $this->update_system_packages = $updateSystemPackages;

        return $this;
    }

    /**
     * Get update_system_packages
     *
     * @return integer 
     */
    public function getUpdateSystemPackages()
    {
        return $this->update_system_packages;
    }

    /**
     * Set packages
     *
     * @param string $packages
     * @return Project
     */
    public function setPackages($packages)
    {
        $this->packages = $packages;

        return $this;
    }

    /**
     * Get packages
     *
     * @return string 
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Set script
     *
     * @param string $script
     * @return Project
     */
    public function setScript($script)
    {
        $this->script = $script;

        return $this;
    }

    /**
     * Get script
     *
     * @return string 
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set services
     *
     * @param string $services
     * @return Project
     */
    public function setServices($services)
    {
        $this->services = $services;

        return $this;
    }

    /**
     * Get services
     *
     * @return string 
     */
    public function getServices()
    {
        return $this->services;
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
     * Set prod_proxy
     *
     * @param Proxy $prodProxy
     * @return Project
     */
    public function setProdProxy(Proxy $prodProxy = null)
    {
        $this->prod_proxy = $prodProxy;

        return $this;
    }

    /**
     * Get prod_proxy
     *
     * @return Proxy
     */
    public function getProdProxy()
    {
        return $this->prod_proxy;
    }

    /**
     * Set test_proxy
     *
     * @param Proxy $testProxy
     * @return Project
     */
    public function setTestProxy(Proxy $testProxy = null)
    {
        $this->test_proxy = $testProxy;

        return $this;
    }

    /**
     * Get test_proxy
     *
     * @return Proxy
     */
    public function getTestProxy()
    {
        return $this->test_proxy;
    }

    /**
     * Set account_id
     *
     * @param integer $accountId
     * @return Project
     */
    public function setAccountId($accountId)
    {
        $this->account_id = $accountId;

        return $this;
    }

    /**
     * Get account_id
     *
     * @return integer 
     */
    public function getAccountId()
    {
        return $this->account_id;
    }

    /**
     * Set prod_credential_id
     *
     * @param integer $prodCredentialId
     * @return Project
     */
    public function setProdCredentialId($prodCredentialId)
    {
        $this->prod_credential_id = $prodCredentialId;

        return $this;
    }

    /**
     * Get prod_credential_id
     *
     * @return integer 
     */
    public function getProdCredentialId()
    {
        return $this->prod_credential_id;
    }

    /**
     * Set test_credential_id
     *
     * @param integer $testCredentialId
     * @return Project
     */
    public function setTestCredentialId($testCredentialId)
    {
        $this->test_credential_id = $testCredentialId;

        return $this;
    }

    /**
     * Get test_credential_id
     *
     * @return integer 
     */
    public function getTestCredentialId()
    {
        return $this->test_credential_id;
    }

    /**
     * Set prod_proxy_id
     *
     * @param integer $prodProxyId
     * @return Project
     */
    public function setProdProxyId($prodProxyId)
    {
        $this->prod_proxy_id = $prodProxyId;

        return $this;
    }

    /**
     * Get prod_proxy_id
     *
     * @return integer 
     */
    public function getProdProxyId()
    {
        return $this->prod_proxy_id;
    }

    /**
     * Set test_proxy_id
     *
     * @param integer $testProxyId
     * @return Project
     */
    public function setTestProxyId($testProxyId)
    {
        $this->test_proxy_id = $testProxyId;

        return $this;
    }

    /**
     * Get test_proxy_id
     *
     * @return integer 
     */
    public function getTestProxyId()
    {
        return $this->test_proxy_id;
    }
}
