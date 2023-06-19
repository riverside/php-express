<?php
namespace PhpExpress;

class Response
{
    /**
     * @var Application
     */
    public $app;

    protected $statusCode = 0;

    protected $statusMessage = NULL;

    protected $headers = array();

    public $headersSent = 0;

    public $locals = array();

    const STATUS_CODES = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',                 // RFC 2518, obsoleted by RFC 4918
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',               // RFC 4918
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',           // RFC 7231
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',         // RFC 7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a Teapot',              // RFC 7168
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',       // RFC 4918
        423 => 'Locked',                     // RFC 4918
        424 => 'Failed Dependency',          // RFC 4918
        425 => 'Unordered Collection',       // RFC 4918
        426 => 'Upgrade Required',           // RFC 2817
        428 => 'Precondition Required',      // RFC 6585
        429 => 'Too Many Requests',          // RFC 6585
        431 => 'Request Header Fields Too Large', // RFC 6585
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',    // RFC 2295
        507 => 'Insufficient Storage',       // RFC 4918
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',               // RFC 2774
        511 => 'Network Authentication Required' // RFC 6585
    );

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->headersSent = headers_sent() ? 1 : 0;
    }

    public function append(string $field, $value): Response
    {
        $prev = $this->get($field);

        if ($prev) {
            if (is_array($prev)) {
                $value = array_merge($prev, array($value));
            } elseif (is_array($value)) {
                array_unshift($value, $prev);
            } else {
                $value = array($prev, $value);
            }
        }

        return $this->set($field, $value);
    }

    public function clearCookie(string $name, array $options=array()): Response
    {
        $options = array_merge(array(
            'expire' => time() - 3600,
            'path' => '/'
        ), $options);

        return $this->cookie($name, "", $options);
    }

    public function contentType(string $type): Response
    {
        return $this->type($type);
    }

    public function cookie(string $name, string $value, array $options=array()): Response
    {
        $expire = isset($options['expire']) ? $options['expire'] : 0;
        $path = isset($options['path']) ? $options['path'] : "";
        $domain = isset($options['domain']) ? $options['domain'] : "";
        $secure = isset($options['secure']) ? $options['secure'] : false;
        $httpOnly = isset($options['httpOnly']) ? $options['httpOnly'] : false;
        if (isset($options['sameSite']))
        {
            if (is_string($options['sameSite']) && in_array(strtolower($options['sameSite']), array("lax", "strict"))) {
                $sameSite = strtolower($options['sameSite']);
            } elseif (is_bool($options['sameSite'])) {
                if (!$options['sameSite']) {
                    $sameSite = "lax";
                } elseif ($options['sameSite']) {
                    $sameSite = "strict";
                }
            }
            if (isset($sameSite)) {
                $path .= "; samesite=" . $sameSite;
            }
        }

        setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);

        return $this;
    }

    public function end(string $data = NULL): void
    {
        if ($data)
        {
            echo $data;
        }
        exit;
    }

    public function get(string $field): ?string
    {
        $field = strtolower($field);
        foreach (headers_list() as $header) {
            list ($name, $value) = explode(":", $header, 2);
            if (strtolower($name) == $field) {
                return trim($value);
            }
        }

        return null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function header(string $field, $value): Response
    {
        return $this->set($field, $value);
    }

    public function json($body): Response
    {
        $body = json_encode($body);

        if (!$this->get("Content-Type")) {
            $this->type("json");
        }

        return $this->send($body);
    }

    public function location(string $path): Response
    {
        return $this->set("Location", $path);
    }

    public function redirect(string $path, int $status = 302): void
    {
        $this->location($path);

        $this->sendHeaders();
        $this->status($status);
        $this->end();
    }

    public function render(string $view, array $locals = array()): void
    {
        $this->sendHeaders();
        $this->status(200);
        $this->app->render($view, $locals);
    }

    public function send($body): Response
    {
        $this->sendHeaders();

        if ($body) {
            switch (true) {
                case is_string($body):
                    if (!$this->get('Content-Type')) {
                        $this->type('html');
                    }
                    break;
                case is_array($body):
                case is_object($body):
                case is_numeric($body):
                case is_bool($body):
                    if (!is_null($body)) {
                        return $this->json($body);
                    }
                    break;
            }
        }

        $this->end($body);

        return $this;
    }

    protected function sendHeader(string $header, bool $replace=true, int $statusCode=0): Response
    {
        if ($statusCode)
        {
            header($header, $replace, $statusCode);
        } else {
            header($header, $replace);
        }

        return $this;
    }

    protected function proceedHeader(string $name, $value): Response
    {
        if (is_string($value) && strlen($value) > 0)
        {
            $this->sendHeader("$name: $value");
        } elseif ($value === false) {
            $this->removeHeader($name);
        }
        
        return $this;
    }
    
    protected function removeHeader(string $name): Response
    {
        header_remove($name);
        
        return $this;
    }

    protected function sendHeaders(): Response
    {
        foreach ($this->headers as $name => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $val)
                {
                    $this->proceedHeader($name, $val);
                }
            } else {
                $this->proceedHeader($name, $value);
            }
        }

        return $this;
    }

    public function sendStatus(int $code): Response
    {
        return $this->type("text")->status($code)->send($this->statusMessage);
    }

    public function set($field, $value=NULL): Response
    {
        if (is_array($field)) {
            foreach ($field as $key => $val) {
                $this->headers[$key] = $val;
            }
        } else {
            $this->headers[$field] = $value;
        }

        return $this;
    }

    public function status(int $code): Response
    {
        $this->statusCode = $code;

        $this->statusMessage = isset(self::STATUS_CODES[$code]) ? self::STATUS_CODES[$code] : "$code";

        $this->sendHeader("HTTP/1.1 $this->statusCode $this->statusMessage");

        return $this;
    }

    public function type(string $value): Response
    {
        $value = strtolower($value);

        switch ($value) {
            case "html":
                $value = "text/html";
                break;
            case "js":
            case "javascript":
                $value = "application/javascript";
                break;
            case "json":
                $value = "application/json";
                break;
            case "css":
                $value = "text/css";
                break;
            case "text":
                $value = "text/plain";
                break;
            case "png":
            case "gif":
            case "webp":
                $value = "image/$value";
                break;
            case "jpg":
            case "jpeg":
                $value = "image/jpeg";
                break;
        }

        return $this->set("Content-Type", $value);
    }

    public function vary(string $value): Response
    {
        return $this->set("Vary", $value);
    }
}