<?php


namespace kitten\system\core;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernel;

class AppKernel extends HttpKernel
{
    /** @var ContainerInterface */
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
        $dispatcher=$this->container->get(EventDispatcherInterface::class);
        $resolver=new ControllerResolver($this->container);
        $requestStack=new RequestStack();
        $argumentResolver=new ArgumentResolver($this->container);
        parent::__construct($dispatcher, $resolver, $requestStack, $argumentResolver);
    }
}