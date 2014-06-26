<?php
namespace Hyperion\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity
 * @ORM\Table(name="actions")
 */
class Action implements HyperionEntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="actions")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getProjectId")
     */
    protected $project;

    /**
     * @ORM\ManyToOne(targetEntity="Environment", inversedBy="actions")
     * @ORM\JoinColumn(name="environment_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getEnvironmentId")
     */
    protected $environment;

    /**
     * @ORM\ManyToOne(targetEntity="Distribution", inversedBy="instances")
     * @ORM\JoinColumn(name="distribution_id", referencedColumnName="id", onDelete="CASCADE")
     *
     * @Serializer\Type("integer")
     * @Serializer\Accessor(getter="getDistributionId")
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

    /**
     * @ORM\Column(type="text")
     */
    protected $workflow_data;

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

    /**
     * Set Workflow Data
     *
     * @param string $workflow_data
     * @return $this
     */
    public function setWorkflowData($workflow_data)
    {
        $this->workflow_data = $workflow_data;
        return $this;
    }

    /**
     * Get Workflow Data
     *
     * @return string
     */
    public function getWorkflowData()
    {
        return $this->workflow_data;
    }

    /**
     * Set environment
     *
     * @param Environment $environment
     * @return Action
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

    public function getDistributionId() {
        return $this->getDistribution() ? $this->getDistribution()->getId() : null;
    }

    public function getProjectId() {
        return $this->getProject() ? $this->getProject()->getId() : null;
    }

    public function getEnvironmentId() {
        return $this->getEnvironment() ? $this->getEnvironment()->getId() : null;
    }

    public function __toString()
    {
        return (string)$this->getId();
    }

}
