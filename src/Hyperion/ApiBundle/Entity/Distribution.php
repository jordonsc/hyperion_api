<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="distributions",indexes={@ORM\Index(name="distro_name", columns={"name"})})
 */
class Distribution implements HyperionEntityInterface
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
    protected $version;

    /**
     * @ORM\ManyToOne(targetEntity="Environment", inversedBy="distributions")
     * @ORM\JoinColumn(name="environment_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getEnvironmentId")
     */
    protected $environment;

    /**
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $tag_string;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $dns;

    /**
     * @ORM\OneToMany(targetEntity="Instance", mappedBy="distribution")
     */
    protected $instances;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $asg;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $image_id;

    // --

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instances = new ArrayCollection();
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
     * @return Distribution
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
     * Set status
     *
     * @param integer $status
     * @return Distribution
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Add instances
     *
     * @param Instance $instances
     * @return Distribution
     */
    public function addInstance(Instance $instances)
    {
        $this->instances[] = $instances;

        return $this;
    }

    /**
     * Remove instances
     *
     * @param Instance $instances
     */
    public function removeInstance(Instance $instances)
    {
        $this->instances->removeElement($instances);
    }

    /**
     * Get instances
     *
     * @return Collection
     */
    public function getInstances()
    {
        return $this->instances;
    }

    /**
     * Set environment
     *
     * @param Environment $environment
     * @return Distribution
     */
    public function setEnvironment(Environment $environment = null)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Get environment
     *
     * @return Environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set build version
     *
     * @param int $version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get build version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set TagString
     *
     * @param string $tag_string
     * @return $this
     */
    public function setTagString($tag_string)
    {
        $this->tag_string = $tag_string;
        return $this;
    }

    /**
     * Get TagString
     *
     * @return string
     */
    public function getTagString()
    {
        return $this->tag_string;
    }

    /**
     * Set Dns
     *
     * @param string $dns
     * @return $this
     */
    public function setDns($dns)
    {
        $this->dns = $dns;
        return $this;
    }

    /**
     * Get Dns
     *
     * @return string
     */
    public function getDns()
    {
        return $this->dns;
    }

    /**
     * Get Asg
     *
     * @return string
     */
    public function getAsg()
    {
        return $this->asg;
    }

    /**
     * Set Asg
     *
     * @param string $asg
     * @return $this
     */
    public function setAsg($asg)
    {
        $this->asg = $asg;
        return $this;
    }

    /**
     * Get ImageId
     *
     * @return string
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Set ImageId
     *
     * @param string $image_id
     * @return $this
     */
    public function setImageId($image_id)
    {
        $this->image_id = $image_id;
        return $this;
    }

    // Serialisers --

    public function getEnvironmentId() {
        return $this->getEnvironment() ? $this->getEnvironment()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getName();
    }
}
