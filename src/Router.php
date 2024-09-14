<?php
declare(strict_types=1);

namespace Riverside\Express;

/**
 * Class Router
 *
 * @package Riverside\Express
 */
class Router
{
    /**
     * @var Route[]
     */
    protected $routes = array();

    /**
     * @var Application
     */
    protected $app;

    /**
     * Router constructor
     *
     * @param Application|null $app
     */
    public function __construct(Application $app = null)
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

    /**
     * Add custom parameters to use in a route path.
     *
     * @param string $name
     * @param string|null $regex
     * @return Router
     */
    public function param(string $name, string $regex = null): Router
    {
        $this->app->param($name, $regex);

        return $this;
    }

    /**
     * Returns an instance of a single route which you can then use to handle HTTP verbs with optional middleware.
     * Use $router->route() to avoid duplicate route naming and thus typing errors.
     *
     * @param string $path
     * @return Route
     */
    public function route(string $path): Route
    {
        $route = new Route($path);
        $route->setApplication($this->app);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        $this->app->router = $this;
        $this->app->run();
    }

    /**
     * Routes HTTP (verb) requests to the specified path with the specified callback function.
     *
     * @param string $name Accepts: delete, get, head, options, patch, post, put, all
     * @param array $arguments
     * @return Router
     */
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