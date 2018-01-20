<?php


namespace kitten\system\core;


use kitten\component\router\RouteGroup;
use kitten\component\router\RouteNode;

interface RouteCollectorInterface
{
    /**
     * @param string $pattern
     * @param callable $callable
     * @return RouteGroup
     */
    public function group(string $pattern,$callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function get(string $pattern, $callable);
    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function post(string $pattern, $callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function put(string $pattern, $callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function patch(string $pattern, $callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function delete(string $pattern, $callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function options(string $pattern, $callable);

    /**
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function any(string $pattern, $callable);

    /**
     * @param string[] $methods
     * @param string $pattern
     * @param $callable
     * @return RouteNode
     */
    public function match(array $methods,string $pattern, $callable);

    /**
     * @return RouteNode[]
     */
    public function getRouteNodes();
}