<?php
declare(strict_types=1);

namespace Riverside\Express\Tests;

use Riverside\Express\Application;
use PHPUnit\Framework\TestCase;
use Riverside\Express\Route;

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

    public function testPath()
    {
        $route = new Route('/');

        $route->setPath('/profile');
        $this->assertSame('/profile', $route->getPath());
    }

    public function testMethod()
    {
        $route = new Route('/');

        $route->setMethod('POST');
        $this->assertSame('POST', $route->getMethod());
    }

    public function testApplication()
    {
        $route = new Route('/');

        $app = new Application();
        $route->setApplication($app);
        $this->assertSame($app, $route->getApplication());
    }

    public function testName()
    {
        $route = new Route('/');

        $route->setName('test');
        $this->assertSame('test', $route->getName());
    }

    public function testCallback()
    {
        $route = new Route('/');

        $route->setCallback('test');
        $this->assertSame('test', $route->getCallback());
    }

    public function testDispatch()
    {
        $route = new Route('/');
        $route->setApplication(new Application());

        $fn = function($req, $res) {
            echo 'test';
        };

        ob_start();
        $this->assertInstanceOf(Route::class, $route->dispatch($fn));
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertSame('test', $content);
    }
}