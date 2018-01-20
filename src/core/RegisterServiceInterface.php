<?php


namespace kitten\system\core;


use kitten\component\container\ExpandContainerInterface;

interface RegisterServiceInterface
{
    public function registerService(ExpandContainerInterface $container);
}