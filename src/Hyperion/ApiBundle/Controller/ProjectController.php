<?php

namespace Hyperion\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use Hyperion\ApiBundle\Entity\Project;
use Hyperion\ApiBundle\Form\ProjectType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends FOSRestController
{

    /**
     * Get all projects
     *
     * @api
     * @Get("/projects")
     * @return Response
     */
    public function getProjectsAction()
    {
        $data = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->findAll();

        return $this->handleView($this->view($data));
    }

    /**
     * Get project by ID
     *
     * @api
     * @Get("/project/{id}")
     * @return Response
     */
    public function getProjectAction($id)
    {
        $data = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        return $this->handleView($this->view($data, $data ? Codes::HTTP_OK : Codes::HTTP_NOT_FOUND));
    }

    /**
     * Create project
     *
     * @api
     * @Post("/project")
     * @param Request $request
     * @return Response
     */
    public function postProjectsActions(Request $request)
    {
        $project = new Project();
        $form    = $this->createForm(new ProjectType(), $project);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->handleView($this->view($project, Codes::HTTP_CREATED));
        } else {
            return $this->handleView($this->view($form, Codes::HTTP_BAD_REQUEST));
        }
    }

    /**
     * Delete a project
     *
     * @api
     * @Delete("/project/{id}")
     * @return Response
     */
    public function deleteProjectAction($id)
    {
        $data = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        if (!$data) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        return $this->handleView($this->view('', Codes::HTTP_OK));
    }

    /**
     * Update a project
     *
     * @api
     * @Put("/project/{id}")
     * @return Response
     */
    public function putProjectAction($id, Request $request)
    {
        $project = $this->getDoctrine()->getRepository('HyperionApiBundle:Project')->find($id);

        if (!$project) {
            return $this->handleView($this->view(null, Codes::HTTP_NOT_FOUND));
        }

        $form = $this->createForm(new ProjectType(), $project);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->handleView($this->view($project, Codes::HTTP_OK));
        } else {
            return $this->handleView($this->view($form, Codes::HTTP_BAD_REQUEST));
        }
    }

}
