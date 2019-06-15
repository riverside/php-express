<?php
namespace PhpExpress;

use PHPUnit\Framework\TestCase;

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