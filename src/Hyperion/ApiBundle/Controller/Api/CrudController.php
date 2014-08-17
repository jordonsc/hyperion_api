<?php

namespace Hyperion\ApiBundle\Controller\Api;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\Dbal\Collection\CriteriaCollection;
use Hyperion\Dbal\Criteria\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * API entity CRUD controller
 */
class CrudController extends FOSRestController
{
    const ERR_UNKNOWN_ENTITY       = "Unknown entity";
    const ERR_INVALID_RELATIONSHIP = "Invalid relationship";

    /**
     * Get the full class name from a short entity name
     *
     * @param $entity
     * @return string
     * @throws NotFoundException
     */
    protected function getClassName($entity)
    {
        if (!$this->get('hyperion.entity_validator')->isValid($entity)) {
            throw new NotFoundException("Unsupported entity '".$entity."'");
        }

        return "Hyperion\\ApiBundle\\Entity\\".Inflector::classify($entity);
    }

    /**
     * Get all projects
     *
     * @api
     * @Get("/{entity}/all")
     * @return Response
     */
    public function getAllEntitiesAction($entity)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->findAll();

        return $this->handleView($this->view($data));
    }

    /**
     * Search projects
     *
     * @api
     * @Post("/{entity}/search")
     * @return Response
     */
    public function searchEntityAction($entity, Request $request)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $sz = $this->get('serializer');

        $criteria = new CriteriaCollection($sz->deserialize(
            $request->getContent(),
            'ArrayCollection<Hyperion\Dbal\Criteria\Criteria>',
            $request->getRequestFormat('json')
        ));

        $search_fields = $this->get('hyperion.entity_validator')->getSearchableFields($entity);
        $dql           = "SELECT e FROM ".$class_name." e WHERE ";
        $where         = [];
        $params        = [];
        $i             = 0;

        /** @var $c Criteria */
        foreach ($criteria as $c) {
            // Sanitise for security -
            if (!in_array($c->getField(), $search_fields) || !$c->getComparison()) {
                return $this->handleView(
                    $this->view("Security violation in criteria index #".$i, Codes::HTTP_FORBIDDEN)
                );
            }

            $params[] = $c->getValue();
            $where[]  = "(e.".$c->getField()." ".$c->getComparison()->value()." ?".($i++).")";
        }

        $query = $em->createQuery($dql.implode(" AND ", $where));
        $out   = $query->setParameters($params)->getResult();

        return $this->handleView($this->view($out));
    }

    /**
     * Create entity
     *
     * @api
     * @Post("/{entity}/new")
     * @param Request $request
     * @return Response
     */
    public function createEntityAction($entity, Request $request)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $entity_class = "\\".$class_name;
        $form_class   = "\\Hyperion\\ApiBundle\\Form\\".Inflector::classify($entity)."Type";

        $obj  = new $entity_class();
        $form = $this->createForm(new $form_class(), $obj);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();
            return $this->handleView($this->view($obj, Codes::HTTP_CREATED));
        } else {
            return $this->handleView($this->view($form, Codes::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete a project
     *
     * @api
     * @Delete("/{entity}/{id}")
     * @return Response
     */
    public function deleteEntityAction($entity, $id)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->find($id);

        if (!$data) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        return $this->handleView($this->view('', Codes::HTTP_OK));

    }

    /**
     * Get entity by ID
     *
     * @api
     * @Get("/{entity}/{id}")
     * @return Response
     */
    public function getEntityAction($entity, $id)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->find($id);
        return $this->handleView($this->view($data, $data ? Codes::HTTP_OK : Codes::HTTP_NOT_FOUND));
    }

    /**
     * Update an entity
     *
     * @api
     * @Put("/{entity}/{id}")
     * @return Response
     */
    public function updateEntityAction($entity, $id, Request $request)
    {
        try {
            $class_name = $this->getClassName($entity);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $form_class = "\\Hyperion\\ApiBundle\\Form\\".Inflector::classify($entity)."Type";
        $obj        = $this->getDoctrine()->getRepository($class_name)->find($id);

        if (!$obj) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $form = $this->createForm(new $form_class(), $obj);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($obj);
            $em->flush();

            return $this->handleView($this->view($obj, Codes::HTTP_OK));
        } else {
            return $this->handleView($this->view($form, Codes::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Get related entities
     *
     * @api
     * @Get("/{entity}/{id}/{relationship}")
     * @return Response
     */
    public function getEntityRelationshipAction($entity, $id, $relationship)
    {
        try {
            $class_name = $this->getClassName($entity);
            $this->getClassName($relationship);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->find($id);

        if (!$data) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        // We need to pluralise the getter name, there could be options here
        // eg 'getAccounts' or 'getCategories'
        $getter    = 'get'.Inflector::classify($relationship);
        $getters[] = $getter.'s';
        $getters[] = substr($getter, 0, -1).'ies';

        $fn = null;
        foreach ($getters as $getter) {
            if (method_exists($data, $getter)) {
                $fn = $getter;
                break;
            }
        }

        if (!$fn) {
            return $this->handleView($this->view(self::ERR_INVALID_RELATIONSHIP, Codes::HTTP_BAD_REQUEST));
        }

        $result = $data->$fn();
        return $this->handleView($this->view($result, Codes::HTTP_OK));
    }

    /**
     * Add a related entity
     *
     * @api
     * @Put("/{entity}/{id}/{relationship}/{add}")
     * @return Response
     */
    public function addEntityRelationshipAction($entity, $id, $relationship, $add)
    {
        try {
            $class_name          = $this->getClassName($entity);
            $class_name_relative = $this->getClassName($relationship);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->find($id);
        if (!$data) {
            return $this->handleView($this->view("Entity not found", Codes::HTTP_NOT_FOUND));
        }

        $relative = $this->getDoctrine()->getRepository($class_name_relative)->find($add);
        if (!$relative) {
            return $this->handleView($this->view("Relative not found", Codes::HTTP_NOT_FOUND));
        }

        $adder = 'add'.Inflector::classify($relationship);
        if (!method_exists($data, $adder)) {
            return $this->handleView($this->view(self::ERR_INVALID_RELATIONSHIP, Codes::HTTP_BAD_REQUEST));
        }

        try {
            $data->$adder($relative);
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
        } catch (\Exception $e) {
            return $this->handleView($this->view("Addition failed", Codes::HTTP_BAD_REQUEST));
        }

        return $this->handleView($this->view(null, Codes::HTTP_OK));
    }

    /**
     * Remove a related entity
     *
     * @api
     * @Delete("/{entity}/{id}/{relationship}/{del}")
     * @return Response
     */
    public function removeEntityRelationshipAction($entity, $id, $relationship, $del)
    {
        try {
            $class_name          = $this->getClassName($entity);
            $class_name_relative = $this->getClassName($relationship);
        } catch (NotFoundException $e) {
            return $this->handleView($this->view(self::ERR_UNKNOWN_ENTITY, Codes::HTTP_NOT_FOUND));
        }

        $data = $this->getDoctrine()->getRepository($class_name)->find($id);
        if (!$data) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $relative = $this->getDoctrine()->getRepository($class_name_relative)->find($del);
        if (!$relative) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $remover = 'remove'.Inflector::classify($relationship);
        if (!method_exists($data, $remover)) {
            return $this->handleView($this->view(self::ERR_INVALID_RELATIONSHIP, Codes::HTTP_BAD_REQUEST));
        }

        try {
            $data->$remover($relative);
            $em = $this->getDoctrine()->getManager();
            $em->persist($data);
            $em->flush();
        } catch (\Exception $e) {
            return $this->handleView($this->view("Removal failed", Codes::HTTP_BAD_REQUEST));
        }

        return $this->handleView($this->view(null, Codes::HTTP_OK));
    }


}
