<?php


namespace kitten\system\events;

use kitten\component\router\RouteResult;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class FindRouteEvent extends BaseEvent
{
    /** @var  Response */
    protected $response;

    /** @var RouteResult */
    protected $routeResult;

    /**
     * @return RouteResult
     */
    public function getRouteResult()
    {
        return $this->routeResult;
    }

    public function __construct(ContainerInterface $container, RouteResult $routeResult)
    {
        parent::__construct($container);
        $this->routeResult=$routeResult;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}