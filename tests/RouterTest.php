<?php
declare(strict_types=1);

namespace Riverside\Express\Tests;

use PHPUnit\Framework\TestCase;
use Riverside\Express\Router;
use Riverside\Express\Route;

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

    public function testRoute()
    {
        $router = new Router();
        $route = $router->route('/');

        $this->assertInstanceOf(Route::class, $route);
    }
}