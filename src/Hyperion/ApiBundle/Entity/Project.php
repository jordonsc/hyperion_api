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
     * @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getAccountId")
     */
    protected $account;

    /**
     * @ORM\OneToMany(targetEntity="Environment", mappedBy="project")
     */
    protected $environments;

    /**
     * @ORM\ManyToMany(targetEntity="Repository", inversedBy="projects")
     * @ORM\JoinTable(name="project_repositories")
     */
    protected $repositories;

    /**
     * @ORM\OneToMany(targetEntity="Action", mappedBy="project")
     */
    protected $actions;

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
    protected $bake_script;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $launch_script;

    /**
     * JSON array
     * @ORM\Column(type="text")
     */
    protected $services;

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
        $this->actions      = new ArrayCollection();
        $this->environments = new ArrayCollection();
        $this->repositories = new ArrayCollection();
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
     * Set the script executed during baking only
     *
     * @param string $script
     * @return Project
     */
    public function setBakeScript($script)
    {
        $this->bake_script = $script;

        return $this;
    }

    /**
     * Get the script executed during baking only
     *
     * @return string 
     */
    public function getBakeScript()
    {
        return $this->bake_script;
    }

    /**
     * Set the script executed during test and production launches
     *
     * @param mixed $launch_script
     * @return $this
     */
    public function setLaunchScript($launch_script)
    {
        $this->launch_script = $launch_script;
        return $this;
    }

    /**
     * Get the script executed during test and production launches
     *
     * @return mixed
     */
    public function getLaunchScript()
    {
        return $this->launch_script;
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
     * Add environment
     *
     * @param Environment $environment
     * @return Project
     */
    public function addEnvironment(Environment $environment)
    {
        $this->environments[] = $environment;

        return $this;
    }

    /**
     * Remove environment
     *
     * @param Environment $environment
     */
    public function removeEnvironment(Environment $environment)
    {
        $this->environments->removeElement($environment);
    }

    /**
     * Get environments
     *
     * @return Collection
     */
    public function getEnvironments()
    {
        return $this->environments;
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
     * Add repositories
     *
     * @param Repository $repositories
     * @return Project
     */
    public function addRepository(Repository $repositories)
    {
        $this->repositories[] = $repositories;

        return $this;
    }

    /**
     * Remove repositories
     *
     * @param Repository $repositories
     */
    public function removeRepository(Repository $repositories)
    {
        $this->repositories->removeElement($repositories);
    }

    /**
     * Get repositories
     *
     * @return Collection
     */
    public function getRepositories()
    {
        return $this->repositories;
    }


    // Serialisers --

    public function getAccountId()
    {
        return $this->getAccount() ? $this->getAccount()->getId() : null;
    }

    public function __toString()
    {
        return $this->getName();
    }

}
