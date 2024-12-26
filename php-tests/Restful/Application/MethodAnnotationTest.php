<?php

namespace Tests\Restful\Application;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\IResourceRouter;
use kalanis\Restful\Application\RouteAnnotation;
use Nette\Application\UI\MethodReflection;
use Nette\Routing\Route;
use Tester\Assert;
use Tests\TestCase;


class MethodAnnotationTest extends TestCase
{

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
