### introduce

<p>The kitten system is a modern library based on the proven and stable Symfony components.</p>
<p>It has the following features:</p>

* Lightweight:<br>
The kitten system is not a full-stack framework, but has the basic functionality of a framework such as HTTP routing, event triggering, service containers, dependency injection, exception handling, and more.

* Flexible:<br>
The kitten system has no views, models, ORMs, emails and other modules. Because it's all up to you, you can choose twig, Smarty as view, Propel, doctrine as ORM.

* Simple:<br>
Each module or function that you need to add can be called on all controllers simply by defining it as a service and registering it in a container.

* performance:<br>
Almost all functions are registered as a service to the container, and only when the service needs to be used, the service is initialized to avoid resource loss.

* Structure<br>
Kitten system does not have a mandatory directory structure, you can organize your code files in own way.

<p>If you feel that the full stack frame (such as Laravel, Symfony, Yii, etc.) is bulky, inflexible, difficult to get started, but unwilling to write your own framework from the very beginning, the kitten system may be a good choice for you.</p>

<p>Getting Started:</p>

```php
<?php

require __DIR__.'/vendor/autoload.php';
use kitten\system\config\AppConfig;
use kitten\system\core\Application;
use kitten\system\core\InitRouteInterface;
use kitten\component\router\RouteCollector;
use kitten\Component\pipeline\MiddlewareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class Home  {
    public function index($id){
       return 'id:'.$id;
    }
}
class Auth implements MiddlewareInterface{
    public function handle(Request $request, Closure $next)
    {
        return new RedirectResponse('/admin/login');
    }
}
class Admin{
    public function index(){
        return 'admin page';
    }
    public function login(){
        return 'login page';
    }
}

class RoutManager implements InitRouteInterface{
    public function init(RouteCollector $route)
    {
        $route->get('/',function (){
           return 'hello world!';
        });
        $route->get('/page/{id}','Home@index');
        $route->group('/admin',function (RouteCollector $router){
            $router->get('','Admin@index')->middleware(Auth::class);
            $router->get('/login','Admin@login');
        });
    }
}

$opt=new AppConfig();
//$opt->setDebug(true);
$app=new Application(new RoutManager(),$opt);
$app->run();
```