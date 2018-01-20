<?php


namespace kitten\system\core;

use kitten\component\container\Container;
use kitten\component\container\ExpandContainerInterface;
use kitten\system\config\AppConfig;
use kitten\system\providers\ExceptionServiceProvider;
use kitten\system\providers\MiddlewareServiceProvider;
use kitten\system\providers\ResultServiceProvider;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use kitten\system\providers\RouterServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Application
{
    /** @var ExpandContainerInterface  */
    protected $container;
    /** @var AppConfig */
    protected $option;
    /** @var Request */
    protected $request;
    /** @var InitRouteInterface  */
    protected $initRoute;
    /** @var RegisterServiceInterface */
    protected $registerService;

    /** @var ExceptionWriteInterface */
    protected $exceptionWrite;


    /**
     * @return AppConfig
     */
    public function getOption()
    {
        return $this->option;
    }
    public function __construct(InitRouteInterface $initRoute, AppConfig $appOption=null, ExpandContainerInterface $container=null, RegisterServiceInterface $registerService=null, ExceptionWriteInterface $exceptionWrite=null,Request $request=null)
    {
        @set_exception_handler(array($this, 'exception_handler'));
        if (is_null($container)){
            $container=new Container();
        }
        if (is_null($appOption)){
            $appOption=new AppConfig();
        }
        $this->option=$appOption;
        if ($appOption->isDebug()){
            $this->enableDebug();
        }else{
            $this->enablePublish();
        }
        $this->container= $container;
        if (empty($request)){
            $request=Request::createFromGlobals();
        }
        $this->request=$request;
        $this->initRoute=$initRoute;
        $this->registerCoreService();
        if (!empty($registerService)){
            $this->registerService=$registerService;
        }
        if (empty($exceptionWrite)){
            $this->exceptionWrite=new ExceptionWrite($this->option,$this->request);
        }else{
            $this->exceptionWrite=$exceptionWrite;
        }
        $this->container->share(ExceptionWriteInterface::class,function () use($exceptionWrite){
            return $this->exceptionWrite;
        });
    }

    /**
     * @return bool
     */
    public function isDebug(){
        return $this->option->isDebug();
    }

    protected function enablePublish(){
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 'Off');
    }
    protected function enableDebug(){
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
//        Debug::enable();
//        ExceptionHandler::register();
    }

    protected function init()
    {
        header_remove("X-Powered-By");
        mb_internal_encoding("UTF-8");
        $this->initService();
        if (!empty($this->registerService)){
            $this->registerService->registerService($this->container);
        }
        $this->container->boot();
    }

    protected function initService(){
        $container=$this->container;
        $container->addServiceProvider(new RouterServiceProvider());
        $container->addServiceProvider(new ResultServiceProvider());
        $container->addServiceProvider(new ExceptionServiceProvider());
        $container->addServiceProvider(new MiddlewareServiceProvider());
    }

    public function run(){
        $this->init();
        $kernel=new AppKernel($this->container);
        $request=$this->request;
        $response = $kernel->handle($request);
        $response->prepare($request);
        $response->send();
        $kernel->terminate($request, $response);
    }
    protected function registerCoreService(){
        $self=$this;
        $container=$this->container;
        $container->share(Request::class,function (){
           return $this->request;
        });
        $container->share(Session::class, function () {
            $session = new Session();
            $session->start();
            return $session;
        });
        $container->share(FlashMessage::class,function (){
            $session=$this->container->get(Session::class);
            return new FlashMessage($session);
        });
        $container->share(AppConfig::class,function () use($self){
            return $self->option;
        });
        $container->share(EventDispatcherInterface::class, function () {
            return new EventDispatcher();
        });
        $container->share(InitRouteInterface::class,function () use($self){
            return $self->initRoute;
        });
        $container->share(CallableResolverInterface::class,function () use ($container){
            return new CallableResolver($container);
        });

    }

    /**
     * @param \Exception $exception
     */
    public function exception_handler($exception) {
        if ($this->option->isDebug()){
            if ($this->request->isXmlHttpRequest()){
                if ($exception instanceof HttpException){
                    $response= new Response($exception->getMessage(),$exception->getStatusCode());
                }else{
                    $response= new Response($exception->getMessage(),500);
                }
                $response->send();
            }else{
                $handler=new  ExceptionHandler();
                $handler->handle($exception);
            }
        }else{
            $write=$this->exceptionWrite;
            if ($this->option->isWriteError() && !empty($this->option->getErrorLogCatalog())){
                $write->write($exception);
            }
            if ($exception instanceof HttpException){
                $code=$exception->getStatusCode();
            }else{
                $code=Response::HTTP_INTERNAL_SERVER_ERROR;
            }
            if ($this->request->isXmlHttpRequest()){
                $response= new Response('Internal Server Error',$code);
                $response->send();
            }else{
                $page500=$this->getOption()->getPage500();
                if (empty($page500)){
                    $page500=dirname(__DIR__).'/resource/page500.html';
                }
                $html= file_get_contents($page500);
                $response=new Response($html,$code);
                $response->send();
            }
        }
    }
}