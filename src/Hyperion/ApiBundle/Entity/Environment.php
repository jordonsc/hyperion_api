<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="environments")
 */
class Environment implements HyperionEntityInterface
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
    protected $environment_type;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="environments")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProjectId")
     */
    protected $project;

    /**
     * @ORM\Column(type="integer")
     */
    protected $tenancy;

    /**
     * @ORM\OneToMany(targetEntity="Distribution", mappedBy="environment")
     */
    protected $distributions;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="environment")
     */
    protected $actions;

    /**
     * @ORM\ManyToOne(targetEntity="Credential", inversedBy="environments")
     * @ORM\JoinColumn(name="credential_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getCredentialId")
     */
    protected $credential;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $zones;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $script;

    /**
     * @ORM\Column(type="string")
     */
    protected $instance_size;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $tags;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $key_pairs;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $firewalls;

    /**
     * @ORM\ManyToOne(targetEntity="Proxy")
     * @ORM\JoinColumn(name="proxy_id", referencedColumnName="id")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProxyId")
     */
    protected $proxy;

    /**
     * @ORM\Column(type="integer")
     */
    protected $private_network;

    /**
     * @ORM\Column(type="integer")
     */
    protected $ssh_port;

    /**
     * @ORM\Column(type="string")
     */
    protected $ssh_user;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ssh_password;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $ssh_pkey;

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
        $this->tags = '[]';
        $this->key_pairs = '[]';
        $this->firewalls = '[]';
        $this->zones = '[]';
        $this->ssh_port = 22;
        $this->private_network = 0;
    }


    /**
     * Set name
     *
     * @param string $name
     * @return Environment
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
     * Set environment_type
     *
     * @param integer $environmentType
     * @return Environment
     */
    public function setEnvironmentType($environmentType)
    {
        $this->environment_type = $environmentType;

        return $this;
    }

    /**
     * Get environment_type
     *
     * @return integer 
     */
    public function getEnvironmentType()
    {
        return $this->environment_type;
    }

    /**
     * Set tenancy
     *
     * @param integer $tenancy
     * @return Environment
     */
    public function setTenancy($tenancy)
    {
        $this->tenancy = $tenancy;

        return $this;
    }

    /**
     * Get tenancy
     *
     * @return integer 
     */
    public function getTenancy()
    {
        return $this->tenancy;
    }

    /**
     * Set availability zones/subnets
     * JSON array
     *
     * @param string $zones
     * @return Environment
     */
    public function setZones($zones)
    {
        $this->zones = $zones;

        return $this;
    }

    /**
     * Get availability zones/subnets
     * JSON array
     *
     * @return string 
     */
    public function getZones()
    {
        return $this->zones;
    }

    /**
     * Set instance_size
     *
     * @param string $instanceSize
     * @return Environment
     */
    public function setInstanceSize($instanceSize)
    {
        $this->instance_size = $instanceSize;

        return $this;
    }

    /**
     * Get instance_size
     *
     * @return string 
     */
    public function getInstanceSize()
    {
        return $this->instance_size;
    }

    /**
     * Set tags
     *
     * @param string $tags
     * @return Environment
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set key pairs
     *
     * @param string $key_pairs
     * @return Environment
     */
    public function setKeyPairs($key_pairs)
    {
        $this->key_pairs = $key_pairs;

        return $this;
    }

    /**
     * Get key pairs
     *
     * @return string 
     */
    public function getKeyPairs()
    {
        return $this->key_pairs;
    }

    /**
     * Set firewalls
     *
     * @param string $firewalls
     * @return Environment
     */
    public function setFirewalls($firewalls)
    {
        $this->firewalls = $firewalls;

        return $this;
    }

    /**
     * Get firewalls
     *
     * @return string 
     */
    public function getFirewalls()
    {
        return $this->firewalls;
    }

    /**
     * Set project
     *
     * @param Project $project
     * @return Environment
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Add distributions
     *
     * @param Distribution $distributions
     * @return Environment
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
     * @return Environment
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
     * Set credential
     *
     * @param Credential $credential
     * @return Environment
     */
    public function setCredential(Credential $credential = null)
    {
        $this->credential = $credential;

        return $this;
    }

    /**
     * Get credential
     *
     * @return Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * Set proxy
     *
     * @param Proxy $proxy
     * @return Environment
     */
    public function setProxy(Proxy $proxy = null)
    {
        $this->proxy = $proxy;

        return $this;
    }

    /**
     * Get proxy
     *
     * @return Proxy
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Set environment script
     *
     * @param string $script
     * @return $this
     */
    public function setScript($script)
    {
        $this->script = $script;
        return $this;
    }

    /**
     * Get environment script
     *
     * @return string
     */
    public function getScript()
    {
        return $this->script;
    }

    /**
     * Set ssh password
     *
     * @param string $ssh_password
     * @return $this
     */
    public function setSshPassword($ssh_password)
    {
        $this->ssh_password = $ssh_password;
        return $this;
    }

    /**
     * Get ssh password
     *
     * @return string
     */
    public function getSshPassword()
    {
        return $this->ssh_password;
    }

    /**
     * Set ssh private key as a string
     *
     * @param string $ssh_pkey
     * @return $this
     */
    public function setSshPkey($ssh_pkey)
    {
        $this->ssh_pkey = $ssh_pkey;
        return $this;
    }

    /**
     * Get ssh private key as a string
     *
     * @return string
     */
    public function getSshPkey()
    {
        return $this->ssh_pkey;
    }

    /**
     * Set the port SSH listens on
     *
     * @param int $ssh_port
     * @return $this
     */
    public function setSshPort($ssh_port)
    {
        $this->ssh_port = $ssh_port;
        return $this;
    }

    /**
     * Get the port SSH listens on
     *
     * @return int
     */
    public function getSshPort()
    {
        return $this->ssh_port;
    }

    /**
     * Set the ssh username
     *
     * @param string $ssh_user
     * @return $this
     */
    public function setSshUser($ssh_user)
    {
        $this->ssh_user = $ssh_user;
        return $this;
    }

    /**
     * Get the ssh username
     *
     * @return string
     */
    public function getSshUser()
    {
        return $this->ssh_user;
    }

    /**
     * Set private network flag
     *
     * @param int $private_network
     * @return $this
     */
    public function setPrivateNetwork($private_network)
    {
        $this->private_network = $private_network;
        return $this;
    }

    /**
     * Get private network flag
     *
     * @return int
     */
    public function getPrivateNetwork()
    {
        return $this->private_network;
    }

    // Serialisers --

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getName();
    }

    public function getProjectId() {
        return $this->getProject() ? $this->getProject()->getId() : null;
    }


    public function getProxyId() {
        return $this->getProxy() ? $this->getProxy()->getId() : null;
    }

    public function getCredentialId() {
        return $this->getCredential() ? $this->getCredential()->getId() : null;
    }

}
