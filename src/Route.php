<?php
namespace PhpExpress;

class Route
{
    protected $callback;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Application
     */
    protected $app;

    const METHODS = array(
        'delete',
        'get',
        'head',
        'options',
        'patch',
        'post',
        'put',
    );

    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setCallback($callback): Route
    {
        $this->callback = $callback;

        return $this;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): Route
    {
        $this->path = $path;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): Route
    {
        $this->method = $method;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Route
    {
        $this->name = $name;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->app;
    }

    public function setApplication(Application $app): Route
    {
        $this->app = $app;

        return $this;
    }

    public function __call(string $name, array $arguments): Route
    {
        $methods = array_merge(self::METHODS, array('all', 'use'));
        if (!in_array($name, $methods))
        {
            trigger_error("The '$name' method is not defined", E_USER_WARNING);
        }

        $this->setMethod($name);
        $this->setCallback($arguments);

        return $this;
    }

    public function dispatch($argument, bool $use = false): Route
    {
        if (is_callable($argument))
        {
            $argument($this->app->getRequest(), $this->app->getResponse());

        } elseif (is_string($argument)) {

	        list($className, $method) = explode("@", $argument);
	
	        try {
	            $reflectionMethod = new \ReflectionMethod($className, $method);
	            if ($use)
	            {
	                echo $reflectionMethod->invoke(new $className, $this->app->getRequest(), $this->app->getResponse(), function () {
	                    //echo '<pre>';
	                    //print_r($this->app->getResponse()->app->router);
	                });
	            } else {
	                echo $reflectionMethod->invoke(new $className, $this->app->getRequest(), $this->app->getResponse());
	            }
	        } catch (\ReflectionException $e) {
	            printf("%s in %s on line %s", $e->getMessage(), $e->getFile(), $e->getLine());
	        } catch (\Exception $e) {
	            printf("%s in %s on line %s", $e->getMessage(), $e->getFile(), $e->getLine());
            }
        }

        return $this;
    }
}