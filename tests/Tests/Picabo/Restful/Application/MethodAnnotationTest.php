<?php

namespace Tests\Picabo\Restful\Application;

require_once __DIR__ . '/../../../bootstrap.php';

use Nette\Application\UI\MethodReflection;
use Picabo\Restful\Application\IResourceRouter;
use Picabo\Restful\Application\RouteAnnotation;
use Nette\Routing\Route;
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

    private RouteAnnotation $methodAnnotation;

    public function testCreateRoutesFromRealPresenterClass1(): void
    {
        $result = $this->methodAnnotation->parse(new MethodReflection(XRoutes::class, 'test'));
        Assert::equal([
            IResourceRouter::GET => '/rest/test',
            IResourceRouter::PUT => '/rest/out',
        ], $result);
    }

    public function testCreateRoutesFromRealPresenterClass2(): void
    {
        $result = $this->methodAnnotation->parse(new MethodReflection(XRoutes::class, 'dummy'));
        Assert::equal([], $result);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->methodAnnotation = new RouteAnnotation();
    }
}

class XRoutes
{
    /**
     * @GET(/rest/test)
     * @PUT(/rest/out)
     * @return void
     */
    public function test(): void
    {
        // for annotation test
    }

    #[XRoute('Test:test')]
    public function dummy(): void
    {
        // for annotation test
    }
}


#[\Attribute]
class XRoute extends Route
{
}


(new MethodAnnotationTest())->run();
