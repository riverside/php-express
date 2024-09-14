<?php
declare(strict_types=1);

namespace Riverside\Express\Tests;

use PHPUnit\Framework\TestCase;
use Riverside\Express\Response;

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