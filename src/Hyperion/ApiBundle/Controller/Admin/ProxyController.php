<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Entity\Proxy;
use Hyperion\ApiBundle\Form\ProxyType;
use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ProxyController extends AdminController
{

    /**
     * Edit proxy
     *
     * @Route("/proxy/{id}", name="admin_proxy")
     * @Method({"GET"})
     * @Template
     */
    public function proxyAction($id)
    {
        if ($id == 'new') {
            $proxy = new Proxy();
        } elseif (substr($id, 0, 4) == 'new:') {
            $account = $this->getDoctrine()->getRepository('HyperionApiBundle:Account')->find(substr($id, 4));
            if (!$account) {
                throw new NotFoundHttpException("Unknown account ID");
            }
            $proxy = new Proxy();
            $proxy->setAccount($account);

        } else {
            $proxy = $this->getDoctrine()->getRepository('HyperionApiBundle:Proxy')->find($id);
        }

        if (!$proxy) {
            throw new NotFoundHttpException("Unknown proxy ID");
        }

        $form_type = new ProxyType();
        $form_type->setWebMode(true);

        $form = $this->createForm(
            $form_type,
            $proxy,
            ['method' => 'POST', 'action' => $this->generateUrl('admin_proxy_save', ['id' => $id])]
        );

        return ['proxy' => $proxy, 'form' => $form->createView()];
    }

    /**
     * Save proxy
     *
     * @Route("/proxy/{id}", name="admin_proxy_save")
     * @Method({"POST"})
     */
    public function proxySaveAction($id, Request $request)
    {
        return $this->saveEntity('Proxy', $id, $request, []);
    }

    /**
     * Delete proxy
     *
     * @Route("/proxy/delete/{id}", name="admin_proxy_delete")
     */
    public function proxyDeleteAction($id, Request $request)
    {
        $proxy = $this->getDoctrine()->getRepository('HyperionApiBundle:Proxy')->find($id);

        if (!$proxy) {
            throw new NotFoundHttpException("Unknown proxy ID");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($proxy);
        $em->flush();

        return $this->redirect($this->generateUrl('admin_account', ['id' => $proxy->getAccount()->getId()]), 303);
    }
}