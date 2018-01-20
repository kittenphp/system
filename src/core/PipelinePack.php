<?php


namespace kitten\system\core;

use kitten\Component\pipeline\MiddlewareInterface;
use kitten\Component\pipeline\Pipeline;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PipelinePack
{
    protected $pipeline;
    protected $oi;
    protected $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container=$container;
        $this->oi=new OIFactory($container);
        $this->pipeline=new Pipeline();
    }

    /**
     * @return Response|null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(){
        $request=$this->container->get(Request::class);
        return $this->pipeline->handle($request);
    }

    /**
     * @param string $className
     */
    public function add(string $className){
        $obj=$this->oi->createInstance($className);
        if ($obj instanceof MiddlewareInterface){
            $this->pipeline->add($obj);
        }else{
            throw new \InvalidArgumentException($className.' must implemented MiddlewareInterface');
        }
    }
}