<?php
declare(strict_types=1);

namespace Riverside\Express\Tests;

use PHPUnit\Framework\TestCase;
use Riverside\Express\Application;

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