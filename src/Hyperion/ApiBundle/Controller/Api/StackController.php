<?php

namespace Hyperion\ApiBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Hyperion\ApiBundle\Exception\NotFoundException;
use Hyperion\ApiBundle\Exception\UnexpectedValueException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StackController extends FOSRestController
{

    /**
     * Bake a project
     *
     * @api
     * @Get("/bake/{id}")
     * @return Response
     */
    public function bakeProjectAction($id)
    {
        try {
            $action_id = $this->get('hyperion.workflow_manager')->bakeById($id);
            $out       = ['action' => $action_id];
            return $this->handleView($this->view($out));
        } catch (UnexpectedValueException $e) {
            return $this->handleView($this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST));
        } catch (NotFoundException $e) {
            return $this->handleView($this->view("Invalid environment ID", Codes::HTTP_NOT_FOUND));
        }
    }

    /**
     * Build an environment
     *
     * @api
     * @Post("/build/{id}")
     * @return Response
     */
    public function buildProjectAction($id, Request $request)
    {
        $name       = $request->get('name', null);
        $tag_string = $request->get('tags', '');

        if (!$name) {
            return $this->handleView($this->view("Build name is required", Codes::HTTP_BAD_REQUEST));
        }

        try {
            $action_id = $this->get('hyperion.workflow_manager')->buildById($id, $name, $tag_string);
            $out       = ['action' => $action_id];
            return $this->handleView($this->view($out));
        } catch (UnexpectedValueException $e) {
            return $this->handleView($this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST));
        } catch (NotFoundException $e) {
            return $this->handleView($this->view("Invalid environment ID", Codes::HTTP_NOT_FOUND));
        }
    }

    /**
     * Deploy an environment
     *
     * @api
     * @Get("/deploy/{id}")
     * @return Response
     */
    public function deployProjectAction($id)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Scale a distribution
     *
     * @api
     * @Get("/scale/{id}/{offset}")
     * @return Response
     */
    public function scaleProjectAction($id, $offset)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Tear-down a distribution
     *
     * @api
     * @Get("/teardown/{id}")
     * @return Response
     */
    public function teardownDistributionAction($id)
    {
        try {
            $action_id = $this->get('hyperion.workflow_manager')->tearDownById($id);
            $out       = ['action' => $action_id];
            return $this->handleView($this->view($out));
        } catch (UnexpectedValueException $e) {
            return $this->handleView($this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST));
        } catch (NotFoundException $e) {
            return $this->handleView($this->view("Invalid distribution ID", Codes::HTTP_NOT_FOUND));
        }
    }

    /**
     * Tear-down all proceeding distributions
     *
     * @api
     * @Get("/teardown-other/{id}")
     * @return Response
     */
    public function teardownOtherDistributionsAction($id)
    {
        try {
            $actions = $this->get('hyperion.workflow_manager')->tearDownOthersById($id);
            $out     = ['actions' => $actions];
            return $this->handleView($this->view($out));
        } catch (UnexpectedValueException $e) {
            return $this->handleView($this->view($e->getMessage(), Codes::HTTP_BAD_REQUEST));
        } catch (NotFoundException $e) {
            return $this->handleView($this->view("Invalid distribution ID", Codes::HTTP_NOT_FOUND));
        }
    }

}
