<?php


namespace kitten\system\events;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

abstract class BaseEvent extends Event
{
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }
    public function getRequest(){
        return $this->container[Request::class];
    }
}