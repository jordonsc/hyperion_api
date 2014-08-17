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

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $private_dns;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $private_ip4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $private_ip6;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $public_dns;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $public_ip4;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $public_ip6;

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

    /**
     * Set private_dns
     *
     * @param string $privateDns
     * @return Instance
     */
    public function setPrivateDns($privateDns)
    {
        $this->private_dns = $privateDns;

        return $this;
    }

    /**
     * Get private_dns
     *
     * @return string 
     */
    public function getPrivateDns()
    {
        return $this->private_dns;
    }

    /**
     * Set private_ip4
     *
     * @param string $privateIp4
     * @return Instance
     */
    public function setPrivateIp4($privateIp4)
    {
        $this->private_ip4 = $privateIp4;

        return $this;
    }

    /**
     * Get private_ip4
     *
     * @return string 
     */
    public function getPrivateIp4()
    {
        return $this->private_ip4;
    }

    /**
     * Set private_ip6
     *
     * @param string $privateIp6
     * @return Instance
     */
    public function setPrivateIp6($privateIp6)
    {
        $this->private_ip6 = $privateIp6;

        return $this;
    }

    /**
     * Get private_ip6
     *
     * @return string 
     */
    public function getPrivateIp6()
    {
        return $this->private_ip6;
    }

    /**
     * Set public_dns
     *
     * @param string $publicDns
     * @return Instance
     */
    public function setPublicDns($publicDns)
    {
        $this->public_dns = $publicDns;

        return $this;
    }

    /**
     * Get public_dns
     *
     * @return string 
     */
    public function getPublicDns()
    {
        return $this->public_dns;
    }

    /**
     * Set public_ip4
     *
     * @param string $publicIp4
     * @return Instance
     */
    public function setPublicIp4($publicIp4)
    {
        $this->public_ip4 = $publicIp4;

        return $this;
    }

    /**
     * Get public_ip4
     *
     * @return string 
     */
    public function getPublicIp4()
    {
        return $this->public_ip4;
    }

    /**
     * Set public_ip6
     *
     * @param string $publicIp6
     * @return Instance
     */
    public function setPublicIp6($publicIp6)
    {
        $this->public_ip6 = $publicIp6;

        return $this;
    }

    /**
     * Get public_ip6
     *
     * @return string 
     */
    public function getPublicIp6()
    {
        return $this->public_ip6;
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
