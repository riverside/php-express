<?php
namespace PhpExpress;

use PHPUnit\Framework\TestCase;

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