<?php
namespace PhpExpress\Tests;

use PHPUnit\Framework\TestCase;
use PhpExpress\Application;

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