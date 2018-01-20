<?php


namespace kitten\system\providers;


use kitten\system\core\EventServiceProvider;
use kitten\system\core\JsonResult;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ResultServiceProvider extends EventServiceProvider
{
    function registerEvent(EventDispatcherInterface $dispatcher, ContainerInterface $container)
    {
        $dispatcher->addListener(KernelEvents::VIEW,function (GetResponseForControllerResultEvent $event){
            $result=$event->getControllerResult();
            if (is_string($result)){
                $event->setResponse(new Response($result));
            }elseif (is_array($result)){
                $event->setResponse(new JsonResponse($result));
            }elseif (is_null($result)){
                $event->setResponse(new Response());
            }elseif (is_numeric($result)){
                $event->setResponse(new Response($result));
            }elseif (is_bool($result)){
                $value=$result?'true':'false';
                $event->setResponse(new Response($value));
            }elseif ($result instanceof JsonResult){
                $event->setResponse(new JsonResponse($result->toArray()));
            }
        });
    }
}