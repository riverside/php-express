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

    public function disable(string $name): self
    {
        return $this->set($name, false);
    }

    public function disabled(string $name): bool
    {
        return !$this->set($name);
    }

    public function enable(string $name): self
    {
        return $this->set($name, true);
    }

    public function enabled(string $name): bool
    {
        return (bool) $this->set($name);
    }

    protected function lazyrouter(): self
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

    public function render(string $view, array $locals=array()): void
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
            $match1 = in_array($route->getMethod(), array('all', strtolower($this->request->method)));
            $match2 = $route->getPath() == $this->request->path;

            if ($match1 && $match2)
            {
                $use = $route->getMethod() == "use";

                foreach ($route->getCallback() as $callback)
                {
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
            }
        }
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

    public function use(string $path): self
    {
        if (func_get_args() < 2)
        {
            return $this;
        }

        $arguments = array_slice(func_get_args(), 1);

        $this->lazyrouter();

        $route = $this->router->route($path);

        call_user_func_array(array($route, 'use'), $arguments);

        return $this;
    }

    public function __call(string $name, array $arguments): self
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