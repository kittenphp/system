<?php


namespace kitten\system\core;


use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

class ControllerResolver implements ControllerResolverInterface
{

    /** @var  ContainerInterface */
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    /**
     * Returns the Controller instance associated with a Request.
     *
     * As several resolvers can exist for a single application, a resolver must
     * return false when it is not able to determine the controller.
     *
     * The resolver must only throw an exception when it should be able to load
     * controller but cannot because of some errors made by the developer.
     *
     * @param Request $request
     * @return array|callable|false A PHP callable representing the Controller, or false if this resolver is not able to determine the controller
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \LogicException If the controller can't be found
     */
    public function getController(Request $request)
    {
        $routeService=$this->container->get(RouteInfoService::class);
        $routeResult=$routeService->getRouteResult();
        $callable= $routeResult->getRouteNode()->getCallable();
        $callableResolver=$this->container->get(CallableResolverInterface::class);
        $result=$callableResolver->resolve($callable);
        if (!is_callable($result)){
            throw new \LogicException('The routing controller is not callable');
        }
        return $result;
    }

    /**
     * Returns the arguments to pass to the controller.
     *
     * @param Request $request A Request instance
     * @param callable $controller A PHP callable
     *
     * @return array An array of arguments to pass to the controller
     *
     * @throws \RuntimeException When value for argument given is not provided
     *
     * @deprecated This method is deprecated as of 3.1 and will be removed in 4.0. Please use the {@see ArgumentResolverInterface} instead.
     */
    public function getArguments(Request $request, $controller)
    {
        // This method is deprecated as of 3.1
        return [];
    }
}