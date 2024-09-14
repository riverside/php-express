<?php
declare(strict_types=1);

namespace Riverside\Express;

/**
 * Class Application
 *
 * @package Riverside\Express
 */
class Application
{
    /**
     * @var Router
     */
    public $router = null;

    /**
     * @var array
     */
    protected $settings = array(
        'views' => 'views',
        'template' => 'layouts/default'
    );

    /**
     * @var array
     */
    protected $locals = array();

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var array
     */
    protected $patterns = array(
        ':id'   => '(\d+)',
        ':hash' => '([a-f\d+]{32})'
    );

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->request = new Request($this);
        $this->response = new Response($this);
    }

    /**
     * Return an instance of response object.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Return an instance of request object.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Sets the Boolean setting name to false
     *
     * @param string $name
     * @return Application
     */
    public function disable(string $name): Application
    {
        return $this->set($name, false);
    }

    /**
     * Returns true if the Boolean setting name is disabled (false)
     *
     * @param string $name
     * @return bool
     */
    public function disabled(string $name): bool
    {
        return !$this->set($name);
    }

    /**
     * Sets the Boolean setting name to true
     *
     * @param string $name
     * @return Application
     */
    public function enable(string $name): Application
    {
        return $this->set($name, true);
    }

    /**
     * Returns true if the setting name is enabled (true)
     *
     * @param string $name
     * @return bool
     */
    public function enabled(string $name): bool
    {
        return (bool) $this->set($name);
    }

    /**
     * Attempt to create an instance of Router, if such not already exists
     *
     * @return Application
     */
    protected function lazyrouter(): Application
    {
        if (!$this->router)
        {
            $this->router = new Router($this);
        }

        return $this;
    }

    /**
     * Get/set local variables to use within the application.
     *
     * @param string|null $name
     * @param null $value
     * @return Application|array|mixed|null
     */
    public function local(string $name = null, $value = null)
    {
        $num = func_num_args();

        if ($num == 0)
        {
            // getter
            return $this->locals;
        }

        if ($num == 1) {
            // getter
            return array_key_exists($name, $this->locals)
                ? $this->locals[$name]
                : null;
        }

        // setter
        $this->locals[$name] = $value;

        return $this;
    }

    /**
     * Add custom parameters to use in a route path.
     *
     * @param string $name
     * @param string|null $regex
     * @return Application
     */
    public function param(string $name, string $regex = null): Application
    {
        $this->patterns[":$name"] = $regex ? "($regex)" : '(.*)';

        return $this;
    }

    /**
     * Returns the rendered HTML of a view. It accepts an optional parameter that is an array containing local variables
     * for the view. It is like $res->render(), except it cannot send the rendered view to the client on its own.
     *
     * @param string $view
     * @param array|null $locals
     */
    public function render(string $view, array $locals=null): void
    {
        if ($locals)
        {
            extract($locals);
        }
        $layout = sprintf("%s/%s.php", $this->set("views"), $view);
        $_template = sprintf("%s/%s.php", $this->set("views"), $this->set("template"));
        if (is_file($_template))
        {
            include $_template;
        } else {
            include $layout;
        }
    }

    /**
     * Returns an instance of a single route, which you can then use to handle HTTP verbs with optional middleware.
     * Use $app->route() to avoid duplicate route names (and thus typo errors).
     *
     * @param string $path
     * @return Route
     */
    public function route(string $path): Route
    {
        $this->lazyrouter();

        $route = $this->router->route($path);

        return $route;
    }

    /**
     * Run the application.
     */
    public function run(): void
    {
        if (!$this->router)
        {
            return;
        }

        foreach ($this->router->getRoutes() as $route)
        {
            $match1 = in_array($route->getMethod(), array('all', 'use', strtolower($this->request->method)));
            if (!$match1)
            {
                continue;
            }
            
            $use = $route->getMethod() == "use";
            $found = 0;

            $pattern = sprintf("#^%s$#", str_replace(array_keys($this->patterns), array_values($this->patterns), $route->getPath()));
            if (!$use)
            {
            	$match2 = null;
                if (!preg_match($pattern, $this->request->path, $match2))
            	{
                    continue;
                } else {
                    $found = 1;
                	//array_shift($match2);
                    $this->request->route = $route->getPath();
                    $this->setParams();
				}
            } else {
                if (!($route->getPath() == '*' || preg_match($pattern, $this->request->path)))
                {
                    continue;
				}
			}

            foreach ($route->getCallback() as $callback)
            {
                $found = 1;
                if (is_array($callback))
                {
                    foreach ($callback as $arg)
                    {
                        $route->dispatch($arg, $use);
                    }
                } else {
                    $route->dispatch($callback, $use);
                }
            }

            if ($found && !$use)
            {
                break;
            }
        }
    }

    /**
     * Set params
     *
     * @return Application
     */
    protected function setParams(): Application
    {
        $route_parts = explode('/', $this->request->route);
        $path_parts = explode('/', $this->request->path);
        foreach ($route_parts as $key => $value)
        {
            if (strpos($value, ':') === 0)
            {
                $this->request->params[substr($value, 1)] = $path_parts[$key];
            }
        }

        return $this;
    }

    /**
     * Assigns setting name to value.
     *
     * @param string $name
     * @param mixed|null $value
     * @return Application|mixed|null
     */
    public function set(string $name, $value = null)
    {
        if (func_num_args() == 1)
        {
            // getter
            return array_key_exists($name, $this->settings)
                ? $this->settings[$name]
                : null;
        }

        // setter
        $this->settings[$name] = $value;

        return $this;
    }

    /**
     * Mounts the specified middleware function at the specified path: the middleware function is executed when
     * the base of the requested path matches path.
     *
     * @return Application
     */
    public function use(): Application
    {
        $num_args = func_num_args();
        if (!$num_args)
        {
            return $this;
        }

        $args = func_get_args();
        switch ($num_args)
        {
            case 1:
                $path = '*';
                $offset = 0;
                break;
            default:
                $path = $args[0];
                $offset = 1;
        }

        $arguments = array_slice($args, $offset);

        $this->lazyrouter();

        $route = $this->router->route($path);

        call_user_func_array(array($route, 'use'), $arguments);

        return $this;
    }

    /**
     * Routes HTTP (verb) requests to the specified path with the specified callback function.
     *
     * @param string $name Accepts: delete, get, head, options, patch, post, put, all
     * @param array $arguments
     * @return Application
     */
    public function __call(string $name, array $arguments): Application
    {
        $methods = array_merge(Route::METHODS, array('all'));
        if (!in_array($name, $methods))
        {
            trigger_error("The '$name' method is not defined", E_USER_WARNING);
        }

        $this->lazyrouter();

        $route = $this->router->route($arguments[0]);
        $route->setMethod($name);
        $route->setCallback(array_slice($arguments, 1));

        //call_user_func_array(array($route, $name), array_slice($arguments, 1));

        return $this;
    }
}