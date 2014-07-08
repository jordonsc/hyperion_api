<?php
namespace Hyperion\ApiBundle\Service;

use Aws\Common\Aws;
use Aws\Swf\SwfClient;
use Doctrine\ORM\EntityManager;
use Hyperion\ApiBundle\Entity\Action;
use Hyperion\ApiBundle\Entity\Distribution;
use Hyperion\ApiBundle\Entity\Environment;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\ApiBundle\Exception\UnexpectedValueException;
use Hyperion\Dbal\Enum\ActionState;
use Hyperion\Dbal\Enum\ActionType;
use Hyperion\Dbal\Enum\DistributionStatus;
use Hyperion\Dbal\Enum\EnvironmentType;

class WorkflowManager
{
    const WORKFLOW_NAME      = 'std_action';
    const WORKFLOW_VERSION   = '1.0.0';
    const TASKLIST           = 'action_worker';
    const ACTION_START_PHASE = 'PENDING';

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
     * @param int $id Environment ID
     * @return int
     * @throws NotFoundException
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
     * @throws UnexpectedValueException
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
        $action->setOutput('');
        $action->setErrorMessage(null);
        $action->setPhase(self::ACTION_START_PHASE);
        $this->em->persist($action);
        $this->em->flush();

        // Create workflow
        $this->createWorkflow('bake-'.$action->getId(), $action->getId());

        return $action->getId();
    }

    /**
     * Build a project by environment ID
     *
     * @param int    $id   Environment ID
     * @param string $name Build name (eg branch)
     * @param string $tag_string
     * @throws NotFoundException
     * @return int
     */
    public function buildById($id, $name, $tag_string)
    {
        $env = $this->em->getRepository('HyperionApiBundle:Environment')->find($id);
        if (!$env) {
            throw new NotFoundException("Environment with ID ".$id." not found");
        }

        return $this->build($env, $name, $tag_string);
    }

    /**
     * Start the bakery process for a project given a BAKERY environment
     *
     * @param Environment $env
     * @param string      $name Build name (eg branch)
     * @param string      $tag_sting
     * @throws UnexpectedValueException
     * @return int Action ID
     */
    public function build(Environment $env, $name, $tag_sting)
    {
        if ($env->getEnvironmentType() != EnvironmentType::TEST) {
            throw new UnexpectedValueException("Cannot build a non-test environment");
        }

        // Normalise the name to something safe
        $name = $this->normaliseDistributionName($name);

        if (strlen($name) < 2) {
            throw new UnexpectedValueException("Build name must be between 2 and 200 chars long");
        }

        // To build a project, we need to create a distribution for it
        $distro = $this->em->createQuery(
            'SELECT d FROM HyperionApiBundle:Distribution d JOIN d.environment e WHERE d.name = :name AND e.project = :prj ORDER BY d.id DESC'
        )->setMaxResults(1)->setParameter('prj', $env->getProject())->setParameter('name', $name)->getOneOrNullResult();

        /** @var Distribution $distro */
        if ($distro) {
            $version = $distro->getVersion() + 1;
        } else {
            $version = 1;
        }

        // Create the distribution for the new build
        $new_distro = new Distribution();
        $new_distro->setName($name);
        $new_distro->setVersion($version);
        $new_distro->setTagString($tag_sting);
        $new_distro->setEnvironment($env);
        $new_distro->setStatus(DistributionStatus::PENDING);
        $this->em->persist($new_distro);

        // Create action record
        $action = new Action();
        $action->setProject($env->getProject());
        $action->setEnvironment($env);
        $action->setActionType(ActionType::BUILD);
        $action->setState(ActionState::ACTIVE);
        $action->setOutput('');
        $action->setErrorMessage(null);
        $action->setPhase(self::ACTION_START_PHASE);
        $action->setDistribution($new_distro);
        $this->em->persist($action);
        $this->em->flush();

        // Create workflow
        $this->createWorkflow('build-'.$action->getId(), $action->getId());

        return $action->getId();
    }

    /**
     * Reduce a distro name to allowed chars, replacing anything else with hyphens
     *
     * Allowed chars:
     *   / \ - _ , . | + :
     *   A-Z
     *   0-9
     *
     * This will also truncate the name to 200 chars, if it is longer.
     *
     * @param string $name
     * @return string
     */
    public function normaliseDistributionName($name)
    {
        $out     = '';
        $len     = strlen($name);
        $allowed = '/\\-_.,|+:';

        if ($len > 200) {
            $name = substr($name, 0, 200);
            $len  = 200;
        }

        for ($i = 0; $i < $len; $i++) {
            $c = $name{$i};
            if (ctype_alnum($c) || strpos($allowed, $c) !== false) {
                $out .= $c;
            } else {
                $out .= '-';
            }
        }

        return $out;
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