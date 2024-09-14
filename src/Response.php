<?php
declare(strict_types=1);

namespace Riverside\Express;

/**
 * Class Response
 *
 * @package Riverside\Express
 */
class Response
{
    /**
     * @var Application
     */
    public $app;

    /**
     * @var int
     */
    protected $statusCode = 0;

    /**
     * @var null|string
     */
    protected $statusMessage = null;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var int
     */
    public $headersSent = 0;

    /**
     * @var array
     */
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

    /**
     * Response constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->headersSent = headers_sent() ? 1 : 0;
    }

    /**
     * Appends the specified value to the HTTP response header field. If the header is not already set, it creates
     * the header with the specified value. The value parameter can be a string or an array.
     *
     * @param string $field
     * @param $value
     * @return Response
     */
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

    /**
     * Sets the HTTP response Content-Disposition header field to "attachment"
     *
     * @param string|null $filename
     * @return Response
     */
    public function attachment(string $filename=null): Response
    {
        $value = $filename
            ? 'attachment; filename="' . basename($filename) . '"'
            : 'attachment';

        $this->set('Content-Disposition', $value);

        if ($filename)
        {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $this->type($ext, true);
        }

        return $this;
    }

    /**
     * Clears the cookie specified by name.
     *
     * @param string $name
     * @param array $options
     * @return Response
     */
    public function clearCookie(string $name, array $options=array()): Response
    {
        $options = array_merge(array(
            'expire' => time() - 3600,
            'path' => '/'
        ), $options);

        return $this->cookie($name, "", $options);
    }

    /**
     * Alias of type()
     *
     * @param string $type
     * @param bool $fallback
     * @return Response
     */
    public function contentType(string $type, bool $fallback=false): Response
    {
        return $this->type($type, $fallback);
    }

    /**
     * Sets cookie name to value. The value parameter may be a string or array converted to JSON.
     *
     * @param string $name
     * @param string $value
     * @param array $options
     * @return Response
     */
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

    /**
     * Transfers the file at path as an "attachment". Typically, browsers will prompt the user for download.
     *
     * @param string $path
     * @param string|null $filename
     */
    public function download(string $path, string $filename=null)
    {
        $name = $filename ? $filename : basename($path);

        $this
            ->set('Pragma', 'public')
            ->set('Expires', 0)
            ->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->set('Cache-Control', 'private')
            ->set('Content-Transfer-Encoding', 'binary');

        $this->attachment($name);

        $length = @filesize($path);
        if ($length > 0)
        {
            $this->set('Content-Length', $length);
        }

        $this->sendHeaders();

        $chunkSize = 1024 * 1024;
        $handle = fopen($path, 'rb');
        while (!feof($handle))
        {
            $buffer = fread($handle, $chunkSize);
            echo $buffer;
            ob_flush();
            flush();
        }
        fclose($handle);
    }

    /**
     * Ends the response process. Use to quickly end the response without any data.
     *
     * @param string|null $data
     */
    public function end(string $data = null): void
    {
        if ($data)
        {
            echo $data;
        }
        exit;
    }

    /**
     * Returns the HTTP response header specified by field. The match is case-insensitive.
     *
     * @param string $field
     * @return string|null
     */
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

    /**
     * Return an array with response headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Alias of set()
     *
     * @param string $field
     * @param $value
     * @return Response
     */
    public function header(string $field, $value): Response
    {
        return $this->set($field, $value);
    }

    /**
     * Sends a JSON response. This method sends a response (with the correct content-type) that is the parameter
     * converted to a JSON string using json_encode().
     *
     * @param mixed $body
     * @param int $flags
     * @return Response
     */
    public function json($body, int $flags = 0): Response
    {
        $body = json_encode($body, $flags);

        if (!$this->get("Content-Type")) {
            $this->type("json");
        }

        return $this->send($body);
    }

    /**
     * Sets the response Location HTTP header to the specified path parameter.
     *
     * @param string $path
     * @return Response
     */
    public function location(string $path): Response
    {
        return $this->set("Location", $path);
    }

    /**
     * Detect mime type
     *
     * @param string $value
     * @return string
     */
    protected static function mimeLookup(string $value)
    {
        $value = strtolower($value);

        switch ($value) {
            case "html":
                $mimeType = "text/html";
                break;
            case "js":
            case "javascript":
                $mimeType = "application/javascript";
                break;
            case "json":
                $mimeType = "application/json";
                break;
            case "css":
                $mimeType = "text/css";
                break;
            case "text":
                $mimeType = "text/plain";
                break;
            case "png":
            case "apng":
            case "gif":
            case "webp":
            case "avif":
                $mimeType = "image/$value";
                break;
            case "jpg":
            case "jpeg":
                $mimeType = "image/jpeg";
                break;
            case "svg":
                $mimeType = "image/svg+xml";
                break;
            case "pdf":
                $mimeType = "application/pdf";
                break;
            default:
                $mimeType = "";
        }

        return $mimeType;
    }

    /**
     * Redirects to the URL derived from the specified path, with specified status, a positive integer that
     * corresponds to an HTTP status code.
     *
     * @param string $path
     * @param int $status
     */
    public function redirect(string $path, int $status = 302): void
    {
        $this->location($path);

        $this->sendHeaders();
        $this->status($status);
        $this->end();
    }

    /**
     * Renders a view and sends the rendered HTML string to the client.
     *
     * @param string $view
     * @param array $locals
     */
    public function render(string $view, array $locals = array()): void
    {
        $this->sendHeaders();
        $this->status(200);
        $this->app->render($view, $locals);
    }

    /**
     * Sends the HTTP response.
     *
     * @param mixed $body
     * @return Response
     */
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

    /**
     * Send a raw HTTP header
     *
     * @param string $header
     * @param bool $replace
     * @param int $statusCode
     * @return Response
     */
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

    /**
     * @param string $name
     * @param string|bool $value
     * @return Response
     */
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

    /**
     * Remove previously set headers
     *
     * @param string $name
     * @return Response
     */
    protected function removeHeader(string $name): Response
    {
        header_remove($name);
        
        return $this;
    }

    /**
     * @return Response
     */
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

    /**
     * Sets the response HTTP status code to statusCode and send its string representation as the response body.
     *
     * @param int $code
     * @return Response
     */
    public function sendStatus(int $code): Response
    {
        return $this->type("text")->status($code)->send($this->statusMessage);
    }

    /**
     * Sets the response's HTTP header field to value. To set multiple fields at once, pass an array as the parameter.
     *
     * @param array|string $field
     * @param mixed|null $value
     * @return Response
     */
    public function set($field, $value=null): Response
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

    /**
     * Sets the HTTP status for the response.
     *
     * @param int $code
     * @return Response
     */
    public function status(int $code): Response
    {
        $this->statusCode = $code;

        $this->statusMessage = isset(self::STATUS_CODES[$code]) ? self::STATUS_CODES[$code] : "$code";

        $this->sendHeader("HTTP/1.1 $this->statusCode $this->statusMessage");

        return $this;
    }

    /**
     * Sets the Content-Type HTTP header to the MIME type as determined by mimeLookup() for the specified type.
     *
     * @param string $value
     * @param bool $fallback
     * @return Response
     */
    public function type(string $value, bool $fallback=false): Response
    {
        $contentType = self::mimeLookup($value);
        if ($contentType)
        {
            $this->set("Content-Type", $contentType);
        } elseif ($fallback) {
            $this->set("Content-Type", "application/octet-stream");
        }

        return $this;
    }

    /**
     * Adds the field to the Vary response header, if it is not there already.
     *
     * @param string $value
     * @return Response
     */
    public function vary(string $value): Response
    {
        return $this->set("Vary", $value);
    }
}