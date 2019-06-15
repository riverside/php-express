<?php
namespace PhpExpress;

use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'router',
            'settings',
            'locals',
            'request',
            'response',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Application::class);
        }
    }
}