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
     * @ORM\Column(type="text", nullable=true)
     */
    protected $output;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $error_message;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phase;

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

    /**
     * Set ErrorMessage
     *
     * @param string $error_message
     * @return $this
     */
    public function setErrorMessage($error_message)
    {
        $this->error_message = $error_message;
        return $this;
    }

    /**
     * Get ErrorMessage
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Set Output
     *
     * @param string $output
     * @return $this
     */
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Get Output
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Set Phase
     *
     * @param string $phase
     * @return $this
     */
    public function setPhase($phase)
    {
        $this->phase = $phase;
        return $this;
    }

    /**
     * Get Phase
     *
     * @return string
     */
    public function getPhase()
    {
        return $this->phase;
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
