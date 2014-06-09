<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

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
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getAccountId")
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
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="prod_projects")
     * @ORM\JoinColumn(name="prod_credential_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProdCredentialId")
     */
    protected $prod_credential;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="test_projects")
     * @ORM\JoinColumn(name="test_credential_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getTestCredentialId")
     */
    protected $test_credential;

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
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $zones;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $script;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $tenancy;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $network_prod;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $network_test;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $instance_size_prod;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $instance_size_test;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $services;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $tags_prod;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $tags_test;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $keys_prod;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $keys_test;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $firewalls_prod;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $firewalls_test;

    /**
     * @ORM\ManyToOne(targetEntity="Proxy")
     * @ORM\JoinColumn(name="prod_proxy_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProdProxyId")
     */
    protected $prod_proxy;

    /**
     * @ORM\ManyToOne(targetEntity="Proxy")
     * @ORM\JoinColumn(name="test_proxy_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getTestProxyId")
     */
    protected $test_proxy;

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
        $this->zones = '[]';
        $this->tags_prod = '[]';
        $this->tags_test = '[]';
        $this->firewalls_prod = '[]';
        $this->firewalls_test = '[]';
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
     * Set zones
     *
     * @param string $zones
     * @return Project
     */
    public function setZones($zones)
    {
        $this->zones = $zones;

        return $this;
    }

    /**
     * Get zones
     *
     * @return string 
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Set instance_size_prod
     *
     * @param string $instanceSizeProd
     * @return Project
     */
    public function setInstanceSizeProd($instanceSizeProd)
    {
        $this->instance_size_prod = $instanceSizeProd;

        return $this;
    }

    /**
     * Get instance_size_prod
     *
     * @return string 
     */
    public function getInstanceSizeProd()
    {
        return $this->instance_size_prod;
    }

    /**
     * Set instance_size_test
     *
     * @param string $instanceSizeTest
     * @return Project
     */
    public function setInstanceSizeTest($instanceSizeTest)
    {
        $this->instance_size_test = $instanceSizeTest;

        return $this;
    }

    /**
     * Get instance_size_test
     *
     * @return string 
     */
    public function getInstanceSizeTest()
    {
        return $this->instance_size_test;
    }

    /**
     * Set tenancy
     *
     * @param string $tenancy
     * @return Project
     */
    public function setTenancy($tenancy)
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    /**
     * Get tenancy
     *
     * @return string 
     */
    public function getTenancy()
    {
        return $this->tenancy;
    }

    /**
     * Set network_prod
     *
     * @param string $networkProd
     * @return Project
     */
    public function setNetworkProd($networkProd)
    {
        $this->network_prod = $networkProd;

        return $this;
    }

    /**
     * Get network_prod
     *
     * @return string 
     */
    public function getNetworkProd()
    {
        return $this->network_prod;
    }

    /**
     * Set network_test
     *
     * @param string $networkTest
     * @return Project
     */
    public function setNetworkTest($networkTest)
    {
        $this->network_test = $networkTest;

        return $this;
    }

    /**
     * Get network_test
     *
     * @return string 
     */
    public function getNetworkTest()
    {
        return $this->network_test;
    }

    /**
     * Set tags_prod
     *
     * @param string $tagsProd
     * @return Project
     */
    public function setTagsProd($tagsProd)
    {
        $this->tags_prod = $tagsProd;

        return $this;
    }

    /**
     * Get tags_prod
     *
     * @return string 
     */
    public function getTagsProd()
    {
        return $this->tags_prod;
    }

    /**
     * Set tags_test
     *
     * @param string $tagsTest
     * @return Project
     */
    public function setTagsTest($tagsTest)
    {
        $this->tags_test = $tagsTest;

        return $this;
    }

    /**
     * Get tags_test
     *
     * @return string 
     */
    public function getTagsTest()
    {
        return $this->tags_test;
    }

    /**
     * Set keys_prod
     *
     * @param string $keysProd
     * @return Project
     */
    public function setKeysProd($keysProd)
    {
        $this->keys_prod = $keysProd;

        return $this;
    }

    /**
     * Get keys_prod
     *
     * @return string 
     */
    public function getKeysProd()
    {
        return $this->keys_prod;
    }

    /**
     * Set keys_test
     *
     * @param string $keysTest
     * @return Project
     */
    public function setKeysTest($keysTest)
    {
        $this->keys_test = $keysTest;

        return $this;
    }

    /**
     * Get keys_test
     *
     * @return string 
     */
    public function getKeysTest()
    {
        return $this->keys_test;
    }

    /**
     * Set firewalls_prod
     *
     * @param string $firewallsProd
     * @return Project
     */
    public function setFirewallsProd($firewallsProd)
    {
        $this->firewalls_prod = $firewallsProd;

        return $this;
    }

    /**
     * Get firewalls_prod
     *
     * @return string 
     */
    public function getFirewallsProd()
    {
        return $this->firewalls_prod;
    }

    /**
     * Set firewalls_test
     *
     * @param string $firewallsTest
     * @return Project
     */
    public function setFirewallsTest($firewallsTest)
    {
        $this->firewalls_test = $firewallsTest;

        return $this;
    }

    /**
     * Get firewalls_test
     *
     * @return string 
     */
    public function getFirewallsTest()
    {
        return $this->firewalls_test;
    }



    // Serialisers --

    public function getAccountId() {
        return $this->getAccount() ? $this->getAccount()->getId() : null;
    }

    public function getTestProxyId() {
        return $this->getTestProxy() ? $this->getTestProxy()->getId() : null;
    }

    public function getProdProxyId() {
        return $this->getProdProxy() ? $this->getProdProxy()->getId() : null;
    }

    public function getTestCredentialId() {
        return $this->getTestCredential() ? $this->getTestCredential()->getId() : null;
    }

    public function getProdCredentialId() {
        return $this->getProdCredential() ? $this->getProdCredential()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getName();
    }

}
