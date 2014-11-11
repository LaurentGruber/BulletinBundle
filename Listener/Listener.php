<?php

namespace Laurent\BulletinBundle\Listener;

use Claroline\CoreBundle\Event\OpenAdministrationToolEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Listener
 * @package Laurent\BulletinBundle\Listener
 * @DI\Service()
 */
class Listener
{
    private $container;

    /**
     * @param ContainerInterface $container
     * @DI\InjectParams({
     *      "container" = @DI\Inject("service_container"),
     *      "requestStack"   = @DI\Inject("request_stack"),
     *     "httpKernel"     = @DI\Inject("http_kernel")
     * })
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, HttpKernelInterface $httpKernel)
    {
        $this->container = $container;
        $this->request = $requestStack->getCurrentRequest();
        $this->httpKernel = $httpKernel;
    }

    /**
     * @DI\Observe("administration_tool_laurent_bulletin_admin_tool")
     *
     * @param OpenAdministrationToolEvent $event
     */
    public function onOpenAdminTool(OpenAdministrationToolEvent $event)
    {
        $params = array();
        $params['_controller'] = 'LaurentBulletinBundle:BulletinAdmin:index';
        $this->redirect($params, $event);
    }


    private function redirect($params, $event)
    {
        $subRequest = $this->request->duplicate(array(), null, $params);
        $response = $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
        $event->setResponse($response);
        $event->stopPropagation();
    }

}
