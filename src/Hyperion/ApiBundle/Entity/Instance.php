<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="instances")
 */
class Instance implements HyperionEntityInterface
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
    protected $instance_id;

    /**
     * @ORM\ManyToOne(targetEntity="Distribution", inversedBy="instances")
     * @ORM\JoinColumn(name="distribution_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getDistributionId")
     */
    protected $distribution;

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
     * Set instance_id
     *
     * @param string $instanceId
     * @return Instance
     */
    public function setInstanceId($instanceId)
    {
        $this->instance_id = $instanceId;

        return $this;
    }

    /**
     * Get instance_id
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instance_id;
    }

    /**
     * Set distribution
     *
     * @param Distribution $distribution
     * @return Instance
     */
    public function setDistribution(Distribution $distribution = null)
    {
        $this->distribution = $distribution;

        return $this;
    }

    /**
     * Get distribution
     *
     * @return Distribution
     */
    public function getDistribution()
    {
        return $this->distribution;
    }

    // Serialisers --

    public function getDistributionId() {
        return $this->getDistribution() ? $this->getDistribution()->getId() : null;
    }

    public function __toString()
    {
        return '['.$this->getId().'] '.$this->getInstanceId();
    }
}
