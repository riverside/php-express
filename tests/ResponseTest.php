<?php
namespace PhpExpress\Tests;

use PHPUnit\Framework\TestCase;
use PhpExpress\Response;

class ResponseTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'app',
            'statusCode',
            'statusMessage',
            'headers',
            'headersSent',
            'locals',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Response::class);
        }
    }
}