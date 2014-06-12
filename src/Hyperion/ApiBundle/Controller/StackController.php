<?php

namespace Hyperion\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
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
        $action_id = $this->get('hyperion.workflow_manager')->bakeById($id);
        $out       = ['action' => $action_id];

        return $this->handleView($this->view($out));
    }

    /**
     * Build a project
     *
     * @api
     * @Get("/build/{id}")
     * @return Response
     */
    public function buildProjectAction($id)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Deploy a project
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
     * Scale a project
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
     * Tear-down a project
     *
     * @api
     * @Get("/teardown/{id}")
     * @return Response
     */
    public function teardownProjectAction($id)
    {
        return $this->handleView($this->view(null));
    }

}
