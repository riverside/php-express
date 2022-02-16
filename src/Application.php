<?php
namespace PhpExpress;

class Application
{
    public $router = null;

    protected $settings = array(
        'views' => 'views',
        'template' => 'layouts/default'
    );

    protected $locals = array();

    protected $request;

    protected $response;

    protected $patterns = array(
        ':id'   => '(\d+)',
        ':hash' => '([a-f\d+]{32})'
    );

    public function __construct()
    {
        $this->request = new Request($this);
        $this->response = new Response($this);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function disable(string $name): Application
    {
        return $this->set($name, false);
    }

    public function disabled(string $name): bool
    {
        return !$this->set($name);
    }

    public function enable(string $name): Application
    {
        return $this->set($name, true);
    }

    public function enabled(string $name): bool
    {
        return (bool) $this->set($name);
    }

    protected function lazyrouter(): Application
    {
        if (!$this->router)
        {
            $this->router = new Router($this);
        }

        return $this;
    }

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

    public function param($name, $regex = null): Application
    {
        $this->patterns[":$name"] = $regex ? "($regex)" : '(.*)';

        return $this;
    }

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

    public function route(string $path): Route
    {
        $this->lazyrouter();

        $route = $this->router->route($path);

        return $route;
    }

    public function run()
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
                	array_shift($match2);
                    $this->setParams($match2);
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

    protected function setParams(array $match2): Application
    {
        foreach ($match2 as $match)
        {
            foreach ($this->patterns as $param => $pattern)
            {
                if (preg_match("#^$pattern$#", $match))
                {
                    $this->request->params[substr($param, 1)] = $match;
                }
            }
        }

        return $this;
    }

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