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
    public $hostname = null;

    /**
     * The IP address from which the user is viewing the current page.
     *
     * @var string|null
     */
    public $ip = null;

    /**
     * Which request method was used to access the page; i.e. 'GET', 'HEAD', 'POST', 'PUT', etc.
     *
     * @var string|null
     */
    public $method = null;

    /**
     * The URI which was given in order to access this page.
     *
     * @var string|null
     */
    public $originalUrl = null;

    /**
     * @var array
     */
    public $params = array();

    /**
     * Contains the path part of the request URL.
     *
     * @var string|null
     */
    public $path = null;

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
    public $protocol = null;

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
    public $scheme = null;

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
     * @var bool
     */
    public $xhr = false;

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
        //$this->route = '';
        $this->scheme = $_SERVER['REQUEST_SCHEME'];
        $this->secure = strtolower($_SERVER['REQUEST_SCHEME']) == 'https';
        $this->session = &$_SESSION;
        $this->xhr = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    }

    /**
     * Checks if the specified content type is acceptable, based on the request's Accept HTTP header field
     *
     * @param string $mimeType
     * @return bool
     */
    public function accept(string $mimeType): bool
    {
        $header = $this->get('Accept');
        if (!$header)
        {
            return false;
        }

        $mimeType = strtolower($mimeType);
        $arr = explode(',', $header);
        $arr = array_map('trim', $arr);
        $arr = array_map('strtolower', $arr);
        foreach ($arr as $item)
        {
            if (strpos($item, ';q=') !== false)
            {
                list($item,) = explode(';q=', $item);
            }

            // Accept: text/html
            // Value: text/html
            if ($item == $mimeType)
            {
                return true;
            }

            // Accept: */*
            if ($item == '*/*')
            {
                return true;
            }

            // Accept: text/*
            // Value: text/html
            if (strpos($item, '/*') !== false
                && substr($item, 0, strpos($item, '/')) == substr($mimeType, 0, strpos($mimeType, '/')))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the specified encoding is acceptable, based on the request's Accept-Encoding HTTP header field
     *
     * @param string $encoding
     * @return bool
     */
    public function acceptEncoding(string $encoding): bool
    {
        $header = $this->get('Accept-Encoding');
        if (!$header)
        {
            return false;
        }

        $encoding = strtolower($encoding);
        $arr = explode(',', $header);
        $arr = array_map('trim', $arr);
        $arr = array_map('strtolower', $arr);
        foreach ($arr as $item)
        {
            $weight = 1;
            if (strpos($item, ';q=') !== false)
            {
                list($item, $weight) = explode(';q=', $item);
            }

            if ($weight == 0 && in_array($item, array('*', 'identity')))
            {
                return false;
            }

            if ($item == '*')
            {
                return true;
            }

            if ($item == $encoding)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if the specified language is acceptable, based on the request's Accept-Language HTTP header field
     *
     * @param string $language
     * @return bool
     */
    public function acceptLanguage(string $language): bool
    {
        $header = $this->get('Accept-Language');
        if (!$header)
        {
            return false;
        }

        $language = strtolower($language);
        $arr = explode(',', $header);
        $arr = array_map('trim', $arr);
        $arr = array_map('strtolower', $arr);
        foreach ($arr as $item)
        {
            if (strpos($item, ';q=') !== false)
            {
                list($item,) = explode(';q=', $item);
            }

            // Accept: en-US
            // Value: en-US
            if ($item == $language)
            {
                return true;
            }

            // Accept: *
            if ($item == '*')
            {
                return true;
            }
        }

        return false;
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