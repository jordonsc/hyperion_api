<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Environment;
use Hyperion\ApiBundle\Form\EnvironmentType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EnvironmentController extends AdminController
{

    /**
     * Edit environment
     *
     * @Route("/environment/{id}", name="admin_environment")
     * @Method({"GET"})
     * @Template
     */
    public function environmentAction($id)
    {
        if ($id == 'new') {
            $environment = new Environment();
        } elseif (substr($id, 0, 4) == 'new:') {
            $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find(substr($id, 4));
            if (!$project) {
                throw new NotFoundHttpException("Unknown project ID");
            }
            $environment = new Environment();
            $environment->setProject($project);

        } else {
            $environment = $this->getDoctrine()->getRepository('HyperionApiBundle:Environment')->find($id);
        }

        if (!$environment) {
            throw new NotFoundHttpException("Unknown environment ID");
        }

        // Convert JSON arrays to \n lists for the forms
        $environment->setFirewalls($this->jsonToList($environment->getFirewalls()));
        $environment->setKeyPairs($this->jsonToList($environment->getKeyPairs()));
        $environment->setTags($this->jsonToListAssoc($environment->getTags()));

        $form_type = new EnvironmentType();
        $form_type->setWebMode(true);

        $form = $this->createForm(
            $form_type,
            $environment,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_environment_save', ['id' => $id])]
        );

        return ['environment' => $environment, 'form' => $form->createView()];
    }

    /**
     * Save environment
     *
     * @Route("/environment/{id}", name="admin_environment_save")
     * @Method({"POST"})
     */
    public function environmentSaveAction($id, Request $request)
    {
        return $this->saveEntity('Environment', $id, $request, ['Firewalls', 'KeyPairs', '!Tags']);
    }

    /**
     * Duplicate environment
     *
     * @Route("/environment/duplicate/{id}", name="admin_environment_duplicate")
     * @Method({"GET"})
     */
    public function environmentDuplicateAction($id, Request $request)
    {
        $environment = $this->getDoctrine()->getRepository('HyperionApiBundle:Environment')->find($id);

        if (!$environment) {
            throw new NotFoundHttpException("Unknown environment ID");
        }

        $new = clone $environment;

        $em = $this->getDoctrine()->getManager();
        $em->persist($new);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_environment', ['id' => $new->getId()]), 303);
    }

    /**
     * Delete environment
     *
     * @Route("/environment/delete/{id}", name="admin_environment_delete")
     */
    public function environmentDeleteAction($id, Request $request)
    {
        $environment = $this->getDoctrine()->getRepository('HyperionApiBundle:Environment')->find($id);

        if (!$environment) {
            throw new NotFoundHttpException("Unknown environment ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($environment);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_project', ['id' => $environment->getProject()->getId()]), 303);
    }
}