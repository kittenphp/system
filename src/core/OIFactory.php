<?php


namespace kitten\system\core;


use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionParameter;

class OIFactory
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
    }

    /**
     * @param string $className
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createInstance(string $className) {
        if (!class_exists($className)){
            throw new \LogicException("CODE:29261,[$className]:the class does not exist");
        }
        $class=new ReflectionClass($className);
        if (!$class->isInstantiable()){
            throw new \LogicException("CODE:29262,[$className] Can not be instantiated");
        }
        $constructor=$class->getConstructor();
        if (is_null($constructor)){
            //If this class does not define a constructor, it is instantiated directly
            return new $className;
        }
        //Get the parameters of the constructor
        $parameters=$constructor->getParameters();
        //Build the value of the parameter from the service container or by default
        $dependencies=$this->getParameterValue($parameters);
        return $class->newInstanceArgs($dependencies);
    }

    /**
     * @param string $className
     * @param string $methodName
     * @param array $paramsValues
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function callMethod(string $className,string $methodName,array $paramsValues=[]){
        $class=new ReflectionClass($className);
        $obj=$this->createInstance($className);
        $method=$class->getMethod($methodName);
        if (!$method->isPublic()){
            throw new \LogicException("CODE:251323,{$method->getName()} Methods are not public types cannot be called");
        }else{
            $dependencies=$this->getMethodParameterValues($className,$methodName,$paramsValues);
            return $method->invokeArgs($obj,$dependencies);
        }
    }

    /**
     * @param mixed $className
     * @param string $methodName
     * @param array $paramsValues
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getMethodParameterValues($className,string $methodName,array $paramsValues=[]){
        $class=new ReflectionClass($className);
        $method=$class->getMethod($methodName);
        $parameters=$method->getParameters();
        if (count($parameters)==0){
            return [];
        }else{
            $dependencies=$this->getParameterValue($parameters,$paramsValues);
            return $dependencies;
        }
    }

    private function getNotClassParameter(ReflectionParameter $parameter,array $paramsValues=[]){
        if (isset($paramsValues[$parameter->getName()])){
            //Get a value from the list of values, such as the fragment match value provided by the route
            $value=$paramsValues[$parameter->getName()];
            return $value;
        }elseif($parameter->isDefaultValueAvailable()) {
            //Return to the default value
            return $parameter->getDefaultValue();
        }else {
            //Throws an exception if the parameter of the method does not specify a type and does not provide a default value
            throw new \LogicException("CODE:251201,{$parameter->getName()} Parameter must specify a type or provide a default value");
        }
    }

    /**
     * @param ReflectionParameter $parameter
     * @return mixed|ContainerInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function getClassParameter(ReflectionParameter $parameter){
        $pClass=$parameter->getClass();
        $className=$pClass->getName();
        if ($this->container->has($className)){
            return $this->container->get($className);
        }else{
            if ($className==ContainerInterface::class){
                return $this->container;
            }else
            {
                throw new \LogicException(("CODE:589750,[$pClass] Not defined as a service within a container"));
            }
        }
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @param array $paramsValues
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getParameterValue ($parameters,array $paramsValues=[]){
        $dependencies = array();
        foreach($parameters as $parameter)
        {
            //Get the definition type of the parameter
            $dependency = $parameter->getClass();
            //Does the parameter specify a specific type?
            if(is_null($dependency))
            {
                //If no type is specified, the value is taken from the default
                $dependencies[] = $this->getNotClassParameter($parameter,$paramsValues);
            }
            else
            {
                //If you specify a type, get the value from the service container
                $dependencies[] = $this->getClassParameter($parameter);
            }
        }
        return $dependencies;
    }
}