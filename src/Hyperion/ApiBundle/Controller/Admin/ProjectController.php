<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Project;
use Hyperion\ApiBundle\Form\ProjectType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AdminController
{

    /**
     * Edit project
     *
     * @Route("/project/{id}", name="admin_project")
     * @Method({"GET"})
     * @Template
     */
    public function projectAction($id)
    {
        if ($id == 'new') {
            $project = new Project();
        } elseif (substr($id, 0, 4) == 'new:') {
            $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find(substr($id, 4));
            if (!$account) {
                throw new NotFoundHttpException("Unknown account ID");
            }
            $project = new Project();
            $project->setAccount($account);

        } else {
            $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);
        }
        if (!$project) {
            throw new NotFoundHttpException("Unknown project ID");
        }

        // Convert JSON arrays to \n lists for the forms
        $project->setPackages($this->jsonToList($project->getPackages()));
        $project->setZones($this->jsonToList($project->getZones()));
        $project->setServices($this->jsonToList($project->getServices()));

        $form = $this->createForm(
            new ProjectType(),
            $project,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_project_post', ['id' => $id])]
        );

        return ['project' => $project, 'form' => $form->createView()];
    }

    /**
     * Save project
     *
     * @Route("/project/{id}", name="admin_project_post")
     * @Method({"POST"})
     */
    public function projectPostAction($id, Request $request)
    {
        return $this->saveEntity('Project', $id, $request, ['Packages', 'Zones', 'Services']);
    }

}