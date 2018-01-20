<?php


namespace kitten\system\core;

use kitten\component\router\RouteCollector;
use kitten\component\router\RouteResult;
use kitten\component\router\RouteTracker;

class RouteInfoService
{
    /** @var RouteCollector */
    protected $routeCollector;
    /** @var RouteTracker */
    protected $routeTracker;
    /** @var RouteResult */
    protected $routeResult;
    /**
     * @return RouteCollector|null
     */
    public function getRouteCollector()
    {
        return $this->routeCollector;
    }

    /**
     * @param RouteCollector $routeCollector
     */
    public function setRouteCollector(RouteCollector $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    /**
     * @return RouteTracker|null
     */
    public function getRouteTracker()
    {
        return $this->routeTracker;
    }

    /**
     * @param RouteTracker $routeTracker
     */
    public function setRouteTracker(RouteTracker $routeTracker)
    {
        $this->routeTracker = $routeTracker;
    }

    /**
     * @return RouteResult|null
     */
    public function getRouteResult()
    {
        return $this->routeResult;
    }

    /**
     * @param RouteResult $routeResult
     */
    public function setRouteResult(RouteResult $routeResult)
    {
        $this->routeResult = $routeResult;
    }
}