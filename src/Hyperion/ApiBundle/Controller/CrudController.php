<?php

namespace Hyperion\ApiBundle\Controller;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Hyperion\ApiBundle\Entity\HyperionEntityInterface;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\Dbal\Collection\CriteriaCollection;
use Hyperion\Dbal\Criteria\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CrudController extends FOSRestController
{

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
        $data = $this->getDoctrine()->getRepository($this->getClassName($entity))->findAll();

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
        $class_name = $this->getClassName($entity);

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
        $class_name   = $this->getClassName($entity);
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
     * Hydrate relationships from their 'ID' fields
     *
     * @param string                  $entity Name of the entity (lowercase)
     * @param HyperionEntityInterface $obj    Actual entity object
     */
    protected function hydrateRelationships($entity, HyperionEntityInterface &$obj)
    {
        $pks = $this->get('hyperion.entity_validator')->getForeignKeys($entity);

        foreach ($pks as $field => $rel_entity) {
            $getter = 'get'.Inflector::classify($field);
            $setter = 'set'.Inflector::classify(substr($field, 0, -3)); // less the _id suffix (see Entity_Rules.md)
            $id     = $obj->$getter();

            if ($id === null) {
                continue;
            }

            $rel = $this->getDoctrine()->getRepository($this->getClassName($rel_entity))->find($id);

            if (!$rel) {
                throw new NotFoundException("Related entity `".$rel_entity."` not found with ID '".$id."'");
            }

            $obj->$setter($rel);
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
        $data = $this->getDoctrine()->getRepository($this->getClassName($entity))->find($id);

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
        $data = $this->getDoctrine()->getRepository($this->getClassName($entity))->find($id);

        return $this->handleView($this->view($data, $data ? Codes::HTTP_OK : Codes::HTTP_NOT_FOUND));
    }

    /**
     * Update an entity
     *
     * @api
     * @Put("/{entity}/{id}")
     * @return Response
     */
    public function updateProjectAction($entity, $id, Request $request)
    {
        $form_class = "\\Hyperion\\ApiBundle\\Form\\".Inflector::classify($entity)."Type";
        $obj        = $this->getDoctrine()->getRepository($this->getClassName($entity))->find($id);

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

}
