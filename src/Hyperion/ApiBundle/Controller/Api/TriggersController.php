<?php

namespace Hyperion\ApiBundle\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TriggersController extends Controller
{

    /**
     * A repository has been updated
     *
     * @Route("/triggers", name="api_triggers")
     */
    public function repoAction(Request $request)
    {
        $host = $request->getClientIp();

        $fn = '/tmp/trigger-'.$host.'-'.time().'.'.rand(100, 999);

        $header = $request->getMethod();
        $all = print_r($request->request->all(), true);

        file_put_contents($fn, $header."\n".$all);

        $out = ['status' => 'OK'];
        return $this->render('HyperionApiBundle::xhr.json.twig', ['data' => $out]);

    }

}
