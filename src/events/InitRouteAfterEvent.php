<?php


namespace kitten\system\events;


use kitten\component\router\RouteCollector;
use Psr\Container\ContainerInterface;

class InitRouteAfterEvent extends BaseEvent
{
    private $routeCollector;

    public function __construct(ContainerInterface $container,RouteCollector $collector)
    {
        parent::__construct($container);
        $this->routeCollector=$collector;
    }
    /**
     * @return RouteCollector
     */
    public function getRouteCollector()
    {
        return $this->routeCollector;
    }
}