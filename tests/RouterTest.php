<?php
namespace PhpExpress\Tests;

use PHPUnit\Framework\TestCase;
use PhpExpress\Router;

class RouterTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'app',
            'routes',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Router::class);
        }
    }
}