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
     * @Get("/project/{id}/bake")
     * @return Response
     */
    public function bakeProject($id)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Deploy a project
     *
     * @api
     * @Get("/project/{id}/deploy")
     * @return Response
     */
    public function deployProject($id)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Scale a project
     *
     * @api
     * @Get("/project/{id}/scale/{offset}")
     * @return Response
     */
    public function scaleProject($id, $offset)
    {
        return $this->handleView($this->view(null));
    }

    /**
     * Tear-down a project
     *
     * @api
     * @Get("/project/{id}/teardown")
     * @return Response
     */
    public function teardownProject($id)
    {
        return $this->handleView($this->view(null));
    }

}
