<?php


namespace kitten\system\providers;

use kitten\system\core\EventServiceProvider;
use kitten\system\core\PipelinePack;
use kitten\system\core\RouteInfoService;
use kitten\system\events\FindRouteEvent;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class MiddlewareServiceProvider extends EventServiceProvider
{
    function registerEvent(EventDispatcherInterface $dispatcher, ContainerInterface $container)
    {
        $dispatcher->addListener(FindRouteEvent::class,function (FindRouteEvent $event)use($container){
            $pack=new PipelinePack($container);
            $routeInfoService=$container->get(RouteInfoService::class);
            $mList=$routeInfoService->getRouteResult()->getRouteNode()->getMiddleware();
            foreach ($mList as $item){
                if (!is_string($item)){
                    throw new \InvalidArgumentException('Middleware must be a string of class name');
                }else{
                    $pack->add($item);
                }
            }
            $response=$pack->handle();
            if (!is_null($response)){
                $event->setResponse($response);
            }
        });
    }
}