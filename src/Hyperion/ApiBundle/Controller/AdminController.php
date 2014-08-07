<?php
namespace Hyperion\ApiBundle\Controller;

use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    use ArraySerialiserTrait;

    /**
     * Admin homepage - list accounts
     *
     * @Route("/", name="admin_index")
     * @Template
     */
    public function homeAction()
    {
        // List accounts
        $accounts = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->findAll();

        return ['accounts' => $accounts];
    }

    /**
     * Account index
     *
     * @Route("/account/{id}", name="admin_account")
     * @Template
     */
    public function accountAction($id)
    {
        $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find($id);
        return ['account' => $account];
    }

    /**
     * Project index
     *
     * @Route("/project/{id}", name="admin_project")
     * @Template
     */
    public function projectAction($id)
    {
        $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        // Convert JSON arrays to \n lists for the forms
        $project->setPackages($this->jsonToList($project->getPackages()));
        $project->setZones($this->jsonToList($project->getZones()));
        $project->setServices($this->jsonToList($project->getServices()));

        $form = $this->createFormBuilder($project)
            ->add('name', 'text')
            ->add('source_image_id', 'text', ['label' => 'Source Image ID'])
            ->add('baked_image_id', 'text', ['label' => 'Baked Image ID', 'read_only' => true])
            ->add('packager', 'choice', ['choices' => [0 => 'YUM', 1 => 'APT']])
            ->add(
                'update_system_packages',
                'choice',
                ['label' => 'Update all system packages when baking', 'choices' => [0 => 'No', 1 => 'Yes']]
            )
            ->add('packages', 'textarea')
            ->add('zones', 'textarea', ['label' => 'Distribution Zones'])
            ->add('bake_script', 'textarea', ['label' => 'Bakery Script'])
            ->add('launch_script', 'textarea', ['label' => 'Launch Script'])
            ->add('services', 'textarea', ['label' => 'System Services'])
            ->add('save', 'submit')
            ->getForm();

        return ['project' => $project, 'form' => $form->createView()];
    }

}