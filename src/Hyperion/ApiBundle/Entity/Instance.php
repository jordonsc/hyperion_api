<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="instances")
 */
class Instance
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
     * @ORM\Column(type="string")
     */
    protected $instance_name;

    /**
     * @ORM\ManyToOne(targetEntity="Distribution", inversedBy="instances")
     * @ORM\JoinColumn(name="distribution_id", referencedColumnName="id")
     */
    protected $distribution;

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

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
     * Set instance_name
     *
     * @param string $instanceName
     * @return Instance
     */
    public function setInstanceName($instanceName)
    {
        $this->instance_name = $instanceName;

        return $this;
    }

    /**
     * Get instance_name
     *
     * @return string
     */
    public function getInstanceName()
    {
        return $this->instance_name;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Instance
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
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
}
