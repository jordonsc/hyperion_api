<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="distributions")
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
     * @ORM\ManyToOne(targetEntity="Environment", inversedBy="distributions")
     * @ORM\JoinColumn(name="environment_id", referencedColumnName="id")
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
     * @ORM\OneToMany(targetEntity="Instance", mappedBy="distribution")
     */
    protected $instances;

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



    // Serialisers --

    public function getEnvironmentId() {
        return $this->getEnvironment() ? $this->getEnvironment()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getName();
    }


}
