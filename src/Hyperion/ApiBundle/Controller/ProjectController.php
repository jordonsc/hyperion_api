<?php

namespace Hyperion\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends FOSRestController
{

    /**
     * @Get("/projects")
     * @return Response
     */
    public function getProjectsAction()
    {
        $data = ['projects' => ['hello', 'world']];

        return $this->handleView($this->view($data));
    }

}
