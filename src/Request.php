<?php
namespace PhpExpress;

/**
 * Class Request
 *
 * @package PhpExpress
 */
class Request
{
    /**
     * @var Application
     */
    public $app;

    /**
     * An associative array of variables passed to the current script via the HTTP POST method when using
     * application/x-www-form-urlencoded or multipart/form-data as the HTTP Content-Type in the request.
     *
     * @var array
     */
    public $body = array();

    /**
     * An associative array of variables passed to the current script via HTTP Cookies.
     *
     * @var array
     */
    public $cookies = array();

    /**
     * An associative array of items uploaded to the current script via the HTTP POST method.
     *
     * @var array
     */
    public $files = array();

    /**
     * Contents of the Host: header from the current request, if there is one.
     *
     * @var string|null
     */
    public $hostname = NULL;

    /**
     * The IP address from which the user is viewing the current page.
     *
     * @var string|null
     */
    public $ip = NULL;

    /**
     * Which request method was used to access the page; i.e. 'GET', 'HEAD', 'POST', 'PUT', etc.
     *
     * @var string|null
     */
    public $method = NULL;

    /**
     * The URI which was given in order to access this page.
     *
     * @var string|null
     */
    public $originalUrl = NULL;

    /**
     * @var array
     */
    public $params = array();

    /**
     * Contains the path part of the request URL.
     *
     * @var string|null
     */
    public $path = NULL;

    /**
     * The port on the server machine being used by the web server for communication.
     * For default setups, this will be '80';
     * using SSL, for instance, will change this to whatever your defined secure HTTP port is.
     *
     * @var int
     */
    public $port;

    /**
     * Name and revision of the information protocol via which the page was requested.
     *
     * @var string|null
     */
    public $protocol = NULL;

    /**
     * An associative array of variables passed to the current script via the URL parameters.
     *
     * @var array
     */
    public $query = array();

    /**
     * Contains the currently-matched route, a string.
     *
     * @var string
     */
    public $route;

    /**
     * Name of the information protocol via which the page was requested.
     *
     * @var string|null
     */
    public $scheme = NULL;

    /**
     * A Boolean property that is true if a TLS connection is established.
     *
     * @var bool
     */
    public $secure = false;

    /**
     * An associative array containing session variables available to the current script.
     *
     * @var array
     */
    public $session = array();

    /**
     * A Boolean property that is true if the request's X-Requested-With header field is "XMLHttpRequest",
     * indicating that the request was issued by a client library such as jQuery.
     *
     * @var int
     */
    public $xhr = 0;

    /**
     * Request constructor
     *
     * @param Application $app
     */
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
        $this->port = (int) $_SERVER['SERVER_PORT'];
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->query = &$_GET;
        $this->route = '';//FIXME;
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->secure = $_SERVER['REQUEST_SCHEME'] == 'https';
        $this->session = &$_SESSION;
        $this->xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ? 1 : 0;
    }

    /**
     * Returns the specified HTTP request header field (case-insensitive match).
     *
     * @param string $name
     * @return string|null
     */
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

    /**
     * Alias of get()
     *
     * @param string $name
     * @return string|null
     */
    public static function header(string $name): ?string
    {
        return self::get($name);
    }

    /**
     * Returns the matching content type if the incoming request's "Content-Type" HTTP header field matches the MIME
     * type specified by the type parameter.
     *
     * @param string $type
     * @return bool
     */
    public static function is(string $type): bool
    {
        $contentType = self::get("content-type");
        list($contentType,) = explode(";", $contentType);

        return $contentType == $type;
    }
}