<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Environment;
use Hyperion\ApiBundle\Entity\Repository;
use Hyperion\ApiBundle\Form\EnvironmentType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Hyperion\Dbal\Exception\ParseException;
use Hyperion\Dbal\Utility\TagStringHelper;
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

    /**
     * Build environment
     *
     * @Route("/environment/build/{id}", name="admin_environment_build")
     * @Method({"POST"})
     */
    public function environmentBakeAction($id, Request $request)
    {
        $environment = $this->getDoctrine()->getRepository('HyperionApiBundle:Environment')->find($id);

        if (!$environment) {
            throw new NotFoundHttpException("Unknown environment ID");
        }

        $repos = $environment->getProject()->getRepositories();

        $build_name = $request->get('build-name');
        $tags       = [];

        /** @var Repository $repo */
        foreach ($repos as $repo) {
            $repo_tag = trim($request->get('repo-'.$repo->getId()));
            if ($repo_tag) {
                $tags[$repo->getId()] = $repo_tag;
            }
        }

        // Check that we have something to build on
        if (!$build_name && !count($tags)) {
            return $this->render(
                'HyperionApiBundle::xhr.json.twig',
                [
                    'data' => [
                        'result'  => 'error',
                        'message' => 'You must specify a build name or provide new repository tags'
                    ]
                ]
            );
        }


        try {
            $tag_helper = new TagStringHelper();
            $tag_string = $tag_helper->buildTagString($tags);

            if (!$build_name) {
                $build_name = $tag_helper->createBuildNameFromTags($tags);
            }
        } catch (ParseException $e) {
            return $this->render(
                'HyperionApiBundle::xhr.json.twig',
                [
                    'data' => [
                        'result'  => 'error',
                        'message' => $e->getMessage()
                    ]
                ]
            );
        }

        try {
            $wf        = $this->get('hyperion.workflow_manager');
            $action_id = $wf->build($environment, $build_name, $tag_string);

            return $this->render(
                'HyperionApiBundle::xhr.json.twig',
                [
                    'data' => [
                        'result'  => 'success',
                        'message' => 'Action ID: '.$action_id
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->render(
                'HyperionApiBundle::xhr.json.twig',
                [
                    'data' => [
                        'result'  => 'error',
                        'message' => $e->getMessage()
                    ]
                ]
            );
        }
    }
}