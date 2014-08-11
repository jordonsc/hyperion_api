<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Repository;
use Hyperion\ApiBundle\Form\RepositoryType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RepositoryController extends AdminController
{

    /**
     * Edit repository
     *
     * @Route("/repository/{id}", name="admin_repository")
     * @Method({"GET"})
     * @Template
     */
    public function repositoryAction($id)
    {
        if ($id == 'new') {
            $repository = new Repository();
        } elseif (substr($id, 0, 4) == 'new:') {
            $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find(substr($id, 4));
            if (!$account) {
                throw new NotFoundHttpException("Unknown account ID");
            }
            $repository = new Repository();
            $repository->setAccount($account);

        } else {
            $repository = $this->getDoctrine()->getRepository('HyperionApiBundle:Repository')->find($id);
        }

        if (!$repository) {
            throw new NotFoundHttpException("Unknown repository ID");
        }

        $form = $this->createForm(
            new RepositoryType(),
            $repository,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_repository_save', ['id' => $id])]
        );

        return ['repository' => $repository, 'form' => $form->createView()];
    }

    /**
     * Save repository
     *
     * @Route("/repository/{id}", name="admin_repository_save")
     * @Method({"POST"})
     */
    public function repositorySaveAction($id, Request $request)
    {
        return $this->saveEntity('Repository', $id, $request, []);
    }

    /**
     * Delete repository
     *
     * @Route("/repository/delete/{id}", name="admin_repository_delete")
     */
    public function repositoryDeleteAction($id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('HyperionApiBundle:Repository')->find($id);

        if (!$repository) {
            throw new NotFoundHttpException("Unknown repository ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($repository);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_account', ['id' => $repository->getAccount()->getId()]), 303);
    }
}