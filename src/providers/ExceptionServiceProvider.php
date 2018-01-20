<?php


namespace kitten\system\providers;


use kitten\system\config\AppConfig;
use kitten\system\core\ExceptionWriteInterface;
use Psr\Container\ContainerInterface;
use kitten\system\core\EventServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class ExceptionServiceProvider extends EventServiceProvider
{
    function registerEvent(EventDispatcherInterface $dispatcher, ContainerInterface $container)
    {
        $self=$this;
        $dispatcher->addListener(KernelEvents::EXCEPTION,function (GetResponseForExceptionEvent $event)use($self,$container){
            /** @var Request $request */
            $request=$container->get(Request::class);
            $ex=$event->getException();
            $opt=$container->get(AppConfig::class);
            $isDebug=$opt->isDebug();
            if ($isDebug){
                throw $ex;
            }else{
                if ($ex instanceof ResourceNotFoundException || $ex instanceof RouteNotFoundException){
                    if ($request->isXmlHttpRequest()){
                        $response=new Response('Not Found',Response::HTTP_NOT_FOUND);
                        $event->setResponse($response);
                    }else{
                        $page404=$container->get(AppConfig::class)->getPage404();
                        $response= $self->handleNotFound($page404);
                        $event->setResponse($response);
                    }
                }else{
                    if ($ex instanceof HttpException){
                        $code=$ex->getStatusCode();
                    }else{
                        $code=Response::HTTP_INTERNAL_SERVER_ERROR;
                    }
                    if ($request->isXmlHttpRequest()){
                        $response=new Response('Server Error',$code);
                        $event->setResponse($response);
                    }else{
                        $write=$container->get(ExceptionWriteInterface::class);
                        $write->write($ex);
                        $page500=$container->get(AppConfig::class)->getPage500();
                        $response= $self->handleError($page500);
                        $event->setResponse($response);
                    }
                }
            }
        });
    }

    /**
     * @param string $page404
     * @return Response
     */
    private function handleNotFound(string $page404){
        if (empty($page404)){
            $page404=dirname(__DIR__).'/resource/page404.html';
        }
        $html= file_get_contents($page404);
        return new Response($html,Response::HTTP_NOT_FOUND);
    }

    /**
     * @param string $page500
     * @param $code
     * @return Response
     */
    private function handleError(string $page500,$code=Response::HTTP_INTERNAL_SERVER_ERROR){
        if (empty($page500)){
            $page500=dirname(__DIR__).'/resource/page500.html';
        }
        $html= file_get_contents($page500);
        return new Response($html,$code);
    }
}