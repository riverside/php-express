<?php
namespace PhpExpress;

class Router
{
    protected $routes = array();

    /**
     * @var Application
     */
    protected $app;

    public function __construct($app = null)
    {
        $this->app = $app !== null && $app instanceof Application
            ? $app
            : new Application();
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function param($name, $regex = null): Router
    {
        $this->app->param($name, $regex);

        return $this;
    }

    public function route(string $path): Route
    {
        $route = new Route($path);
        $route->setApplication($this->app);

        $this->routes[] = $route;

        return $route;
    }

    public function run(): void
    {
        $this->app->router = $this;
        $this->app->run();
    }

    public function __call(string $name, array $arguments): Router
    {
        $methods = array_merge(Route::METHODS, array('all'));
        if (!in_array($name, $methods))
        {
            trigger_error("The '$name' method is not defined", E_USER_WARNING);
        }

        $route = $this->route($arguments[0]);
        $route->setMethod($name);
        $route->setCallback(array_slice($arguments, 1));

        //call_user_func_array(array($route, $name), array_slice($arguments, 1));

        return $this;
    }
}