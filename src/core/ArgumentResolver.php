<?php


namespace kitten\system\core;

use Closure;
use Psr\Container\ContainerInterface;
use ReflectionFunction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;

class ArgumentResolver implements ArgumentResolverInterface
{
    /** @var  ContainerInterface */
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    /**
     * Returns the arguments to pass to the controller
     * @param Request $request
     * @param callable $controller
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \RuntimeException When no value could be provided for a required argument
     */
    public function getArguments(Request $request, $controller)
    {
        if ($this->is_closure($controller)){
            $f = new ReflectionFunction($controller);
            $oi=new OIFactory($this->container);
            $params = $f->getParameters();
            if (empty($params)){
                return [];
            }else{
                $routerService=$this->container->get(RouteInfoService::class);
                $routeArgs=$routerService->getRouteResult()->getCallParameters();
                $arguments= $oi->getParameterValue($params,$routeArgs);
                return $arguments;
            }
        }elseif (is_array($controller)){
            $routerService=$this->container->get(RouteInfoService::class);
            $oi=new OIFactory($this->container);
            $className=$controller[0];
            $methodName=$controller[1];
            $arguments=$oi->getMethodParameterValues($className,$methodName,$routerService->getRouteResult()->getCallParameters());
            return $arguments;
        }else{
            throw new \RuntimeException($controller.':This call method is not supported');
        }
    }


    /**
     * @param $t
     * @return bool
     */
    protected function is_closure($t) {
        return is_object($t) && ($t instanceof Closure);
    }
}