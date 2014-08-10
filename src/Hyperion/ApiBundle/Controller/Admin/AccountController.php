<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Account;
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
        if ($id == 'new') {
            $account = new Account();
        } else {
            $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find($id);
        }

        if (!$account) {
            throw new NotFoundHttpException("Unknown account ID");
        }

        $form = $this->createForm(
            new AccountType(),
            $account,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_account_save', ['id' => $id])]
        );

        return ['account' => $account, 'form' => $form->createView()];
    }

    /**
     * Save account
     *
     * @Route("/account/{id}", name="admin_account_save")
     * @Method({"POST"})
     */
    public function accountSaveAction($id, Request $request)
    {
        return $this->saveEntity('Account', $id, $request, []);
    }

    /**
     * Delete account
     *
     * @Route("/account/delete/{id}", name="admin_account_delete")
     */
    public function accountDeleteAction($id, Request $request)
    {
        $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find($id);

        if (!$account) {
            throw new NotFoundHttpException("Unknown account ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($account);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_index'), 303);
    }


}