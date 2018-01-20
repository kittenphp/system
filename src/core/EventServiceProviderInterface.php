<?php


namespace kitten\system\core;


use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use kitten\component\container\BootServiceProviderInterface;

interface EventServiceProviderInterface extends BootServiceProviderInterface
{
    function registerEvent(EventDispatcherInterface $dispatcher,ContainerInterface $container);
}