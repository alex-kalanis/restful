<?php

namespace Tests\Picabo\Restful\Application;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Application\RouteAnnotation;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\RouteAnnotation.
 *
 * @testCase Tests\Picabo\Restful\Application\MethodAnnotationTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application
 */
class MethodAnnotationTest extends TestCase
{

    private $presenterReflection;

    /** @var RouteAnnotation */
    private $methodAnnotation;

    public function testCreateRoutesFromPresenterActionAnnotations(): void
    {
        $methodReflection = Mockery::mock(\Nette\Utils\ReflectionMethod::class);
        $methodReflection->expects('hasAnnotation')
            ->once()
            ->with('GET')
            ->andReturn(TRUE);
        $methodReflection->expects('getName')
            ->atLeastOnce()
            ->andReturn('actionTest');
        $methodReflection->expects('getAnnotation')
            ->once()
            ->with('GET')
            ->andReturn('test/resource');

        $this->presenterReflection->expects('getMethods')
            ->once()
            ->andReturn([$methodReflection]);

        $this->presenterReflection->expects('getShortName')
            ->once()
            ->andReturn('TestPresenter');

        $routes = $this->methodAnnotation->getRoutes();
        Assert::true(isset($routes['Test:test']));
        Assert::equal($routes['Test:test'], 'test/resource');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->presenterReflection = Mockery::mock(\Nette\Utils\Type::class);
        $this->methodAnnotation = new RouteAnnotation($this->presenterReflection, 'GET');
    }
}

(new MethodAnnotationTest())->run();
