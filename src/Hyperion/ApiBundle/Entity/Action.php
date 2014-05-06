<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="actions")
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="actions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="Distribution", inversedBy="instances")
     * @ORM\JoinColumn(name="distribution_id", referencedColumnName="id")
     */
    protected $distribution;

    /**
     * @ORM\Column(type="integer")
     */
    protected $action_type;


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
     * Set action_type
     *
     * @param integer $actionType
     * @return Action
     */
    public function setActionType($actionType)
    {
        $this->action_type = $actionType;

        return $this;
    }

    /**
     * Get action_type
     *
     * @return integer
     */
    public function getActionType()
    {
        return $this->action_type;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Action
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
     * Set project
     *
     * @param Project $project
     * @return Action
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
     * Set distribution
     *
     * @param Distribution $distribution
     * @return Action
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
