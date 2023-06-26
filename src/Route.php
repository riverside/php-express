<?php
namespace PhpExpress;

/**
 * Class Route
 *
 * @package PhpExpress
 */
class Route
{
    /**
     * @var array|string|callable
     */
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

    /**
     * Route constructor
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    /**
     * Gets a callback
     *
     * @return array|callable|string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets a callback
     *
     * @param array|string|callable $callback
     * @return Route
     */
    public function setCallback($callback): Route
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Gets a path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Sets a path
     *
     * @param string $path
     * @return Route
     */
    public function setPath(string $path): Route
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Gets a method
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Sets a method
     *
     * @param string $method
     * @return Route
     */
    public function setMethod(string $method): Route
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Gets a name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets a name
     *
     * @param string $name
     * @return Route
     */
    public function setName(string $name): Route
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets an application instance
     *
     * @return Application
     */
    public function getApplication(): Application
    {
        return $this->app;
    }

    /**
     * Sets an application
     *
     * @param Application $app
     * @return Route
     */
    public function setApplication(Application $app): Route
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @param string $name Accepts: delete, get, head, options, patch, post, put, all, use
     * @param array $arguments
     * @return Route
     */
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

    /**
     * @param callable|string $argument
     * @param bool $use
     * @return Route
     */
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