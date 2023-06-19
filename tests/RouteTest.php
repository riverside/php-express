<?php
namespace PhpExpress\Tests;

use PHPUnit\Framework\TestCase;
use PhpExpress\Route;

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