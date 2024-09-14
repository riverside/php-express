<?php
declare(strict_types=1);

namespace Riverside\Express\Tests;

use PHPUnit\Framework\TestCase;
use Riverside\Express\Request;

class RequestTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'app',
            'body',
            'cookies',
            'files',
            'hostname',
            'ip',
            'method',
            'originalUrl',
            'path',
            'port',
            'protocol',
            'query',
            'route',
            'scheme',
            'secure',
            'session',
            'xhr',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Request::class);
        }
    }
}