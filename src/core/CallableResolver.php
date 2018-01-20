<?php


namespace kitten\system\core;

use Psr\Container\ContainerInterface;
use RuntimeException;

class CallableResolver implements CallableResolverInterface
{
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    /**
     * @param string $classActionName
     * @return bool
     */
    protected function textMatch(string $classActionName){
        //match 'controllerName@action'
        $pattern='/^[\w\\\\]+@[\w]+$/';
        if (preg_match($pattern,$classActionName)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Invoke the resolved callable.
     *
     * @param mixed $toResolve
     *
     * @return callable
     */
    public function resolve($toResolve)
    {
        if (is_callable($toResolve)) {
            return $toResolve;
        }
        if (!is_string($toResolve)){
            throw new RuntimeException($toResolve.' must be callable or string');
        }
        if (!$this->textMatch($toResolve)){
            throw new \InvalidArgumentException($toResolve.': The format of the string does not meet the specification');
        }
        $array=explode('@', $toResolve);
        $className=$array[0];
        $activeName=$array[1];
        $obj=[$this->generateObj($className),$activeName];
        return $obj;
    }

    protected function generateObj(string $className){
        $oi=new OIFactory($this->container);
        return $oi->createInstance($className);
    }
}