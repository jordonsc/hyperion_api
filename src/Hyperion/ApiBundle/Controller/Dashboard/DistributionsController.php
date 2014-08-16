<?php
namespace Hyperion\ApiBundle\Controller\Dashboard;

use Hyperion\ApiBundle\Entity\Action;
use Hyperion\ApiBundle\Entity\Distribution;
use Hyperion\Dbal\Enum\DistributionStatus;
use Hyperion\Dbal\Enum\EnvironmentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DistributionsController extends Controller
{

    /**
     * @Route("/distributions", name="dashboard_distributions")
     * @Template
     */
    public function distributionsAction()
    {
        return [];
    }

    /**
     * @Route("/distribution-list.json", name="dashboard_distributions_list")
     */
    public function distributionsListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $active = $em->createQuery(
            'SELECT d FROM HyperionApiBundle:Distribution d WHERE d.status < :status ORDER BY d.id DESC'
        )
            ->setParameter('status', DistributionStatus::TERMINATED)->getResult();

        $closed = $em->createQuery(
            'SELECT d FROM HyperionApiBundle:Distribution d WHERE d.status >= :status ORDER BY d.id DESC'
        )
            ->setParameter('status', DistributionStatus::TERMINATED)->setMaxResults(9)->getResult();

        $out         = new \stdClass();
        $out->active = [];
        $out->closed = [];

        foreach ($active as $distribution) {
            $out->active[] = $this->serialiseDistribution($distribution);
        }

        foreach ($closed as $distribution) {
            $out->closed[] = $this->serialiseDistribution($distribution);
        }

        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => $out]);
    }

    /**
     * @Route("/distribution/rebuild/{id}", name="dashboard_rebuild_distribution")
     */
    public function distributionRebuildAction($id)
    {
        $distribution = $this->getDoctrine()->getRepository('HyperionApiBundle:Distribution')->find($id);

        if (!$distribution) {
            throw new NotFoundHttpException("Unknown distribution ID");
        }

        $wf = $this->get('hyperion.workflow_manager');
        $wf->build($distribution->getEnvironment(), $distribution->getName(), $distribution->getTagString());

        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => ['result' => 'success']]);
    }

    /**
     * @Route("/distribution/teardown/{id}", name="dashboard_teardown_distribution")
     */
    public function distributionTearDownAction($id)
    {
        $distribution = $this->getDoctrine()->getRepository('HyperionApiBundle:Distribution')->find($id);

        if (!$distribution) {
            throw new NotFoundHttpException("Unknown distribution ID");
        }

        $wf = $this->get('hyperion.workflow_manager');
        $wf->tearDown($distribution);

        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => ['result' => 'success']]);
    }

    /**
     * Convert the Action object into a serialisable \stdClass object
     *
     * @param Distribution $distribution
     * @return \stdClass
     */
    protected function serialiseDistribution(Distribution $distribution)
    {
        $out                   = new \stdClass();
        $out->id               = $distribution->getId();
        $out->name             = $distribution->getName();
        $out->version          = $distribution->getVersion();
        $out->status           = $distribution->getStatus();
        $out->project_id       = $distribution->getEnvironment()->getProject()->getId();
        $out->project_name     = $distribution->getEnvironment()->getProject()->getName();
        $out->environment_id   = $distribution->getEnvironment()->getId();
        $out->environment_name = $distribution->getEnvironment()->getName();
        $out->environment_type = $distribution->getEnvironment()->getEnvironmentType();

        if ($distribution->getEnvironment()->getEnvironmentType() == EnvironmentType::PRODUCTION) {
            $out->instances = count($distribution->getInstances());
        } else {
            $instances      = $distribution->getInstances();
            $out->instances = $instances->count() ? $instances->current()->getInstanceId() : '-';
        }
        return $out;
    }


} 