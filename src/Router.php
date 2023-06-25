<?php
namespace PhpExpress;

/**
 * Class Router
 *
 * @package PhpExpress
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
     * Router constructor.
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

    public function run(): void
    {
        $this->app->router = $this;
        $this->app->run();
    }

    /**
     * @param string $name
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