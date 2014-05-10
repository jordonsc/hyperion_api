<?php
namespace Hyperion\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class StaticController extends Controller
{

    /**
     * @Route("/")
     * @Template
     */
    public function homeAction()
    {
        return [];
    }

}
 