<?php
namespace PhpExpress;

use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
    public function testSuccess()
    {
        $attributes = array(
            'callback',
            'method',
            'path',
            'name',
            'app',
        );
        foreach ($attributes as $attribute) {
            $this->assertClassHasAttribute($attribute, Route::class);
        }
    }
}