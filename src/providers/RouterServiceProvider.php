<?php


namespace kitten\system\providers;

use kitten\component\container\BootServiceProviderInterface;
use kitten\component\container\ExpandContainerInterface;
use kitten\component\router\RouteCollector;
use kitten\component\router\RouteTracker;
use kitten\system\core\InitRouteInterface;
use kitten\system\core\RouteCollectorInterface;
use kitten\system\core\RouteInfoService;
use kitten\system\events\FindRouteEvent;
use kitten\system\events\InitRouteAfterEvent;
use kitten\system\events\NotFindRouteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class RouterServiceProvider implements BootServiceProviderInterface
{
    function register(ExpandContainerInterface $container)
    {
        $container->share(RouteInfoService::class,function (){
           return new RouteInfoService();
        });
        $container->share(RouteCollectorInterface::class,function (){
           return new RouteCollector();
        });
    }

    function boot(ExpandContainerInterface $container=null)
    {
        $e=$container->get(EventDispatcherInterface::class);
        $e->addListener(KernelEvents::REQUEST,function (GetResponseEvent $event) use($container,$e){
            $routeInfoServer=$container->get(RouteInfoService::class);
            $request=$event->getRequest();
            $routeCollector=$container->get(RouteCollectorInterface::class);
            $routeInfoServer->setRouteCollector($routeCollector);
            //Initialize the routing node definition
            $bootRoute=$container->get(InitRouteInterface::class);
            $bootRoute->init($routeCollector);
            //Trigger events, allowing dynamic increase routing at this time
            $initRouteAfterEvent=new InitRouteAfterEvent($container,$routeCollector);
            $e->dispatch( InitRouteAfterEvent::class,$initRouteAfterEvent);
            $tracker=new RouteTracker($routeCollector->getRouteNodes());
            $routeInfoServer->setRouteTracker($tracker);
            $url=$request->getPathInfo();
            $method=$request->getMethod();
            $result=$tracker->search($url,$method);
            if (is_null($result)){
                $notFindRouteEvent=new NotFindRouteEvent($container);
                $e->dispatch(NotFindRouteEvent::class,$notFindRouteEvent);
                if (is_null($notFindRouteEvent->getResponse())){
                    throw new RouteNotFoundException("URL:{$url} method:{$method}");
                }else{
                    $event->setResponse($notFindRouteEvent->getResponse());
                }
            }else{
                $routeInfoServer->setRouteResult($result);
                //Trigger FindRouteEvent event
                $findRouteEvent=new FindRouteEvent($container,$result);
                $e->dispatch(FindRouteEvent::class,$findRouteEvent);
                if (!empty($findRouteEvent->getResponse())) {
                    $event->setResponse($findRouteEvent->getResponse());
                }
            }
        });
    }
}