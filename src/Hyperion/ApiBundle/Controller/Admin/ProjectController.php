<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Project;
use Hyperion\ApiBundle\Entity\Repository;
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
        $project->setServices($this->jsonToList($project->getServices()));

        $form_type = new ProjectType();
        $form_type->setWebMode(true);

        $form = $this->createForm(
            $form_type,
            $project,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_project_save', ['id' => $id])]
        );

        return ['project' => $project, 'form' => $form->createView()];
    }

    /**
     * Save project
     *
     * @Route("/project/{id}", name="admin_project_save")
     * @Method({"POST"})
     */
    public function projectSaveAction($id, Request $request)
    {
        return $this->saveEntity('Project', $id, $request, ['Packages', 'Services']);
    }

    /**
     * Delete project
     *
     * @Route("/project/delete/{id}", name="admin_project_delete")
     */
    public function projectDeleteAction($id, Request $request)
    {
        $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        if (!$project) {
            throw new NotFoundHttpException("Unknown project ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($project);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_account', ['id' => $project->getAccount()->getId()]), 303);
    }

    /**
     * Get project branches
     *
     * @Route("/project/{id}/branches", name="admin_project_branches")
     * @Method({"GET"})
     */
    public function projectBranchesAction($id)
    {
        $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        if (!$project) {
            throw new NotFoundHttpException("Unknown project ID");
        }

        $out = [];
        $repos = $project->getRepositories();

        /** @var Repository $repo */
        foreach ($repos as $repo) {
            $out[$repo->getId()] = ['name' => $repo->getName(), 'tag' => $repo->getTag()];
        }

        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => $out]);
    }
}