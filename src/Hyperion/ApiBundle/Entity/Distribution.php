<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="distrubutions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

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
     * Set project
     *
     * @param Project $project
     * @return Distribution
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
}
