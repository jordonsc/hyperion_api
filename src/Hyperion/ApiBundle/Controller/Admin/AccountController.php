<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Form\AccountType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AdminController
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
     * Edit account
     *
     * @Route("/account/{id}", name="admin_account")
     * @Template
     * @Method({"GET"})
     */
    public function accountAction($id)
    {
        $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find($id);
        if (!$account) {
            throw new NotFoundHttpException("Unknown account ID");
        }

        $form = $this->createForm(
            new AccountType(),
            $account,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_account_post', ['id' => $id])]
        );

        return ['account' => $account, 'form' => $form->createView()];
    }

    /**
     * Save account
     *
     * @Route("/account/{id}", name="admin_account_post")
     * @Method({"POST"})
     */
    public function accountPostAction($id, Request $request)
    {
        return $this->saveEntity('Account', $id, $request, []);
    }


}