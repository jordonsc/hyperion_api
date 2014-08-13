<?php
namespace Hyperion\ApiBundle\Controller\Dashboard;

use Hyperion\ApiBundle\Entity\Action;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends Controller
{

    /**
     * @Route("/activity", name="dashboard_activity")
     * @Template
     */
    public function activityAction()
    {
        return [];
    }

    /**
     * @Route("/activities.json", name="dashboard_activity_list")
     */
    public function activityListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $active = $em->createQuery('SELECT a FROM HyperionApiBundle:Action a WHERE a.state < :state ORDER BY a.id DESC')
            ->setParameter('state', 2)->getResult();

        $closed = $em->createQuery('SELECT a FROM HyperionApiBundle:Action a WHERE a.state > :state ORDER BY a.id DESC')
            ->setParameter('state', 1)->setMaxResults(9)->getResult();

        $out         = new \stdClass();
        $out->active = [];
        $out->closed = [];

        foreach ($active as $action) {
            $out->active[] = $this->serialiseAction($action);
        }

        foreach ($closed as $action) {
            $out->closed[] = $this->serialiseAction($action);
        }

        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => $out]);
    }

    /**
     * @Route("/activity/{id}/output.{_format}", name="dashboard_activity_output", defaults={"_format": "html"}, requirements={"_format": "html|txt"})
     * @Template
     */
    public function activityOutputAction($id)
    {
        $action = $this->getDoctrine()->getRepository('HyperionApiBundle:Action')->find($id);
        if (!$action) {
            throw new NotFoundHttpException("Unknown action ID");
        }

        return ['output' => $action->getOutput()];
    }

    /**
     * Convert the Action object into a serialisable \stdClass object
     *
     * @param Action $action
     * @return \stdClass
     */
    protected function serialiseAction(Action $action)
    {
        $out                = new \stdClass();
        $out->id            = $action->getId();
        $out->project_id    = $action->getProject()->getId();
        $out->project_name  = $action->getProject()->getName();
        $out->action_type   = $action->getActionType();
        $out->error_message = $action->getErrorMessage();
        $out->phase         = $action->getPhase();
        $out->state         = $action->getState();

        if ($action->getDistribution()) {
            $out->distribution_id   = $action->getDistribution()->getId();
            $out->distribution_name = $action->getDistribution()->getName();
        } else {
            $out->distribution_id   = null;
            $out->distribution_name = null;
        }

        if ($action->getEnvironment()) {
            $out->environment_id   = $action->getEnvironment()->getId();
            $out->environment_name = $action->getEnvironment()->getName();
            $out->environment_type = $action->getEnvironment()->getEnvironmentType();
        } else {
            $out->environment_id   = null;
            $out->environment_name = null;
            $out->environment_type = null;
        }

        return $out;
    }


} 