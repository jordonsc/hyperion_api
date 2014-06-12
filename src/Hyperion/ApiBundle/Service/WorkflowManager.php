<?php
namespace Hyperion\ApiBundle\Service;

use Aws\Common\Aws;
use Aws\Swf\SwfClient;
use Doctrine\ORM\EntityManager;
use Hyperion\ApiBundle\Entity\Action;
use Hyperion\ApiBundle\Entity\Environment;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\ApiBundle\Exception\UnexpectedValueException;
use Hyperion\Dbal\Enum\ActionState;
use Hyperion\Dbal\Enum\ActionType;
use Hyperion\Dbal\Enum\EnvironmentType;

class WorkflowManager
{
    const WORKFLOW_NAME    = 'std_action';
    const WORKFLOW_VERSION = '1.0.0';
    const TASKLIST         = 'action_worker';

    /**
     * @var array
     */
    protected $config;

    /**
     * @var Aws
     */
    protected $aws;

    /**
     * @var SwfClient
     */
    protected $swf;

    /**
     * @var EntityManager
     */
    protected $em;

    public function __construct($em, array $config)
    {
        $this->em     = $em;
        $this->config = $config;
        $this->aws    = Aws::factory($config);
        $this->swf    = $this->aws->get('swf');
    }

    /**
     * Bake a project by environment ID
     *
     * @param $id
     * @return int
     */
    public function bakeById($id)
    {
        $env = $this->em->getRepository('HyperionApiBundle:Environment')->find($id);
        if (!$env) {
            throw new NotFoundException("Environment with ID ".$id." not found");
        }

        return $this->bake($env);
    }

    /**
     * Start the bakery process for a project given a BAKERY environment
     *
     * @param Environment $env
     * @return int Action ID
     */
    public function bake(Environment $env)
    {
        if ($env->getEnvironmentType() != EnvironmentType::BAKERY) {
            throw new UnexpectedValueException("Cannot bake a non-bakery environment");
        }

        // Create action record
        $action = new Action();
        $action->setProject($env->getProject());
        $action->setEnvironment($env);
        $action->setActionType(ActionType::BAKE);
        $action->setState(ActionState::ACTIVE);
        $action->setWorkflowData('[]');
        $this->em->persist($action);
        $this->em->flush();

        // Create workflow
        $this->createWorkflow('bake-'.$action->getId(), $action->getId());

        return $action->getId();
    }

    /**
     * Create a new SWF workflow execution
     *
     * @param string $workflow_id      Workflow ID
     * @param string $input            Workflow input
     * @param int    $workflow_timeout Workflow timeout in seconds
     * @param int    $task_timeout     Default task timeout in seconds
     */
    protected function createWorkflow($workflow_id, $input, $workflow_timeout = 3600, $task_timeout = 300)
    {
        $this->swf->startWorkflowExecution(
            [
                'domain'                       => $this->config['domain'],
                'workflowId'                   => $workflow_id,
                'workflowType'                 => [
                    'name'    => self::WORKFLOW_NAME,
                    'version' => self::WORKFLOW_VERSION,
                ],
                'taskList'                     => [
                    'name' => self::TASKLIST,
                ],
                'input'                        => $input,
                'executionStartToCloseTimeout' => (string)$workflow_timeout,
                'taskStartToCloseTimeout'      => (string)$task_timeout,
                'childPolicy'                  => 'TERMINATE',
            ]
        );
    }

}