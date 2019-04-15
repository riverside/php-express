<?php
namespace PhpExpress;

class Request
{
    public $app;
    public $cookies = array();
    public $body = array();
    public $hostname = NULL;
    public $ip = NULL;
    public $method = NULL;
    public $originalUrl = NULL;
    public $path = NULL;
    public $route;
    public $protocol = NULL;
    public $query = array();
    public $scheme = NULL;
    public $xhr = 0;

    public function __construct($app)
    {
        if (isset($_GET['_path_'])) {
            $path = $_GET['_path_'];
            unset($_GET['_path_']);
        } else {
            $path = '/';
        }

        $this->app = $app;
        $this->cookies = $_COOKIE;
        $this->body = $_POST;
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->originalUrl = $_SERVER['REQUEST_URI'];
        $this->path = $path;
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->query = $_GET;
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? 1 : 0;
    }

    public function get(string $name): ?string
    {
        $name = strtolower($name);

        $headers = apache_request_headers();

        foreach ($headers as $header => $value)
        {
            $header = strtolower($header);
            if ($header == $name)
            {
                return $value;
            }
        }

        return null;
    }

    public function header(string $name): ?string
    {
        return $this->get($name);
    }

    public function is(string $type): bool
    {
        $contentType = $this->get("content-type");
        list($contentType,) = explode(";", $contentType);

        return $contentType == $type;
    }
}