<?php
namespace Hyperion\ApiBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class HyperionKernelListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $event->getRequest()->setFormat('yml', 'text/yaml');
    }
}
