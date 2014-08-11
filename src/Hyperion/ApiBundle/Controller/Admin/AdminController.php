<?php
namespace Hyperion\ApiBundle\Controller\Admin;

use Hyperion\ApiBundle\Traits\ArraySerialiserTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminController extends Controller
{
    use ArraySerialiserTrait;

    /**
     * Generic entity save function
     *
     * @param string   $entity
     * @param int|null $id
     * @param Request  $request
     * @param array    $list_fields
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws NotFoundHttpException
     */
    protected function saveEntity($entity, $id, Request $request, array $list_fields = [])
    {
        if (substr($id, 0, 3) == 'new') {
            $id = null;
        }

        $entity_class = '\Hyperion\ApiBundle\Entity\\'.$entity;
        $entity_lc    = strtolower($entity);

        $object = $id ?
            $this->getDoctrine()->getRepository('HyperionApiBundle:'.$entity)->find($id) :
            new $entity_class();

        if (!$object) {
            throw new NotFoundHttpException("Unknown ".$entity_lc." ID");
        }

        $type_class = '\Hyperion\ApiBundle\Form\\'.$entity.'Type';
        $form       = $this->createForm(new $type_class(), $object);
        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($list_fields as $list_field) {
                if ($list_field{0} == '!') {
                    $assoc      = true;
                    $list_field = substr($list_field, 1);
                } else {
                    $assoc = false;
                }

                $setter = 'set'.$list_field;
                $getter = 'get'.$list_field;

                if ($assoc) {
                    $object->$setter($this->listToJsonAssoc($object->$getter()));
                } else {
                    $object->$setter($this->listToJson($object->$getter()));
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($object);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_'.$entity_lc, ['id' => $object->getId()]), 303);
        } else {
            $out    = ['success' => false, 'errors' => []];
            $errors = $form->getErrors();
            foreach ($errors as $error) {
                /** @var FormError $error */
                $out['errors'][] = $error->getMessage();
            }
            $out['extra'] = $form->getExtraData();
            return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => $out]);
        }
    }

}