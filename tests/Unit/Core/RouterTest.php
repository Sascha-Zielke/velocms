<?php

declare(strict_types=1);

namespace VeloCMS\Tests\Unit\Core;

use PHPUnit\Framework\TestCase;
use VeloCMS\Core\Router;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset routes between tests via reflection
        $reflection = new \ReflectionClass(Router::class);
        $property   = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function testGet_registersGetRoute(): void
    {
        Router::get('/test', 'SomeController@index');

        $reflection = new \ReflectionClass(Router::class);
        $property   = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $routes = $property->getValue();

        $this->assertCount(1, $routes);
        $this->assertSame('GET', $routes[0]['method']);
        $this->assertSame('/test', $routes[0]['path']);
        $this->assertSame('SomeController@index', $routes[0]['handler']);
    }

    public function testPost_registersPostRoute(): void
    {
        Router::post('/save', 'SomeController@save');

        $reflection = new \ReflectionClass(Router::class);
        $property   = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $routes = $property->getValue();

        $this->assertCount(1, $routes);
        $this->assertSame('POST', $routes[0]['method']);
    }

    public function testBuildPattern_convertsNamedSegment(): void
    {
        $reflection = new \ReflectionClass(Router::class);
        $method     = $reflection->getMethod('buildPattern');
        $method->setAccessible(true);

        $pattern = $method->invoke(null, '/blog/[*:slug]');

        $this->assertMatchesRegularExpression($pattern, '/blog/my-post');
        $this->assertDoesNotMatchRegularExpression($pattern, '/blog/');
    }
}
