<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="schemas")
 */
class Schema implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Project", mappedBy="schema")
     */
    protected $project;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="string")
     */
    protected $image_id;

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
     * @ORM\OneToOne(targetEntity="Proxy")
     */
    protected $prod_proxy;

    /**
     * @ORM\OneToOne(targetEntity="Proxy")
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
     * Set type
     *
     * @param integer $type
     * @return Schema
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set image_id
     *
     * @param string $imageId
     * @return Schema
     */
    public function setImageId($imageId)
    {
        $this->image_id = $imageId;

        return $this;
    }

    /**
     * Get image_id
     *
     * @return string 
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Set packager
     *
     * @param integer $packager
     * @return Schema
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
     * @return Schema
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
     * @return Schema
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
     * @return Schema
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
     * @return Schema
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
     * Set project
     *
     * @param Project $project
     * @return Schema
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
     * Set prod_proxy
     *
     * @param Proxy $prodProxy
     * @return Schema
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
     * @return Schema
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
}
