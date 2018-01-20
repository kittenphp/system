<?php


namespace kitten\system\core;


use kitten\component\container\ExpandContainerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class EventServiceProvider implements EventServiceProviderInterface
{
    function boot(ExpandContainerInterface $container)
    {
        $dispatcher = $container->get(EventDispatcherInterface::class);
        $this->registerEvent($dispatcher,$container);
    }
    abstract function registerEvent(EventDispatcherInterface $dispatcher, ContainerInterface $container);
    function register(ExpandContainerInterface $container){}
}