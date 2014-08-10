<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Credential;
use Hyperion\ApiBundle\Form\CredentialType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CredentialController extends AdminController
{

    /**
     * Edit credential
     *
     * @Route("/credential/{id}", name="admin_credential")
     * @Method({"GET"})
     * @Template
     */
    public function credentialAction($id)
    {
        if ($id == 'new') {
            $credential = new Credential();
        } elseif (substr($id, 0, 4) == 'new:') {
            $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find(substr($id, 4));
            if (!$account) {
                throw new NotFoundHttpException("Unknown account ID");
            }
            $credential = new Credential();
            $credential->setAccount($account);

        } else {
            $credential = $this->getDoctrine()->getRepository('HyperionApiBundle:Credential')->find($id);
        }

        if (!$credential) {
            throw new NotFoundHttpException("Unknown credential ID");
        }

        $form = $this->createForm(
            new CredentialType(),
            $credential,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_credential_save', ['id' => $id])]
        );

        return ['credential' => $credential, 'form' => $form->createView()];
    }

    /**
     * Save credential
     *
     * @Route("/credential/{id}", name="admin_credential_save")
     * @Method({"POST"})
     */
    public function credentialSaveAction($id, Request $request)
    {
        return $this->saveEntity('Credential', $id, $request, []);
    }

    /**
     * Delete credential
     *
     * @Route("/credential/delete/{id}", name="admin_credential_delete")
     */
    public function credentialDeleteAction($id, Request $request)
    {
        $credential = $this->getDoctrine()->getRepository('HyperionApiBundle:Credential')->find($id);

        if (!$credential) {
            throw new NotFoundHttpException("Unknown credential ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($credential);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_account', ['id' => $credential->getAccount()->getId()]), 303);
    }
}