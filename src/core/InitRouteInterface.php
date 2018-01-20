<?php


namespace kitten\system\core;
use kitten\component\router\RouteCollector;

interface InitRouteInterface
{
    public function init(RouteCollector $route);
}