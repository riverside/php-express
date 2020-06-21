<?php
namespace PhpExpress;

class Request
{
    public $app;
    public $body = array();
    public $cookies = array();
    public $files = array();
    public $hostname = NULL;
    public $ip = NULL;
    public $method = NULL;
    public $originalUrl = NULL;
    public $params = array();
    public $path = NULL;
    public $port;
    public $protocol = NULL;
    public $query = array();
    public $route;
    public $scheme = NULL;
    public $secure = false;
    public $session = array();
    public $xhr = 0;

    public function __construct(Application $app)
    {
        if (isset($_GET['_path_'])) {
            $path = $_GET['_path_'];
            unset($_GET['_path_']);
        } else {
            $path = '/';
        }

        $this->app = $app;
        $this->body = &$_POST;
        $this->cookies = &$_COOKIE;
        $this->files = &$_FILES;
        $this->hostname = $_SERVER['HTTP_HOST'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->originalUrl = $_SERVER['REQUEST_URI'];
        $this->path = $path;
        $this->port = $_SERVER['SERVER_PORT'];
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->query = &$_GET;
        $this->route = '';//FIXME;
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->secure = $_SERVER['REQUEST_SCHEME'] == 'https';
        $this->session = &$_SESSION;
        $this->xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? 1 : 0;
    }

    public static function get(string $name): ?string
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

    public static function header(string $name): ?string
    {
        return self::get($name);
    }

    public static function is(string $type): bool
    {
        $contentType = self::get("content-type");
        list($contentType,) = explode(";", $contentType);

        return $contentType == $type;
    }
}