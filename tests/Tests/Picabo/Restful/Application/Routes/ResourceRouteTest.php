<?php

namespace Tests\Picabo\Restful\Application\Routes;

require_once __DIR__ . '/../../../../bootstrap.php';

use Picabo\Restful\Application\Routes\ResourceRoute;
use Picabo\Restful\Application\IResourceRouter;
use Mockista\MockInterface;
use Nette;
use Nette\Http\IRequest;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Routes\ResourceRoute.
 *
 * @testCase Tests\Picabo\Restful\Application\Routes\ResourceRouteTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Routes
 */
class ResourceRouteTest extends TestCase
{

    /** @var ResourceRoute */
    private $route;

    /** @var MockInterface */
    private $httpRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->route = new ResourceRoute('resources/test', array(
            'module' => 'Resources',
            'presenter' => 'Test',
            'action' => array(
                IResourceRouter::GET => 'read',
                IResourceRouter::POST => 'create',
                IResourceRouter::PUT => 'update',
                IResourceRouter::DELETE => 'delete',
            )
        ), IResourceRouter::CRUD);
        $this->httpRequest = $this->mockista->create(\Nette\Http\IRequest::class);
    }

    public function testRouteListeningOnCrudRequestMethods(): void
    {
        Assert::true($this->route->isMethod(IResourceRouter::GET));
        Assert::true($this->route->isMethod(IResourceRouter::PUT));
        Assert::true($this->route->isMethod(IResourceRouter::POST));
        Assert::true($this->route->isMethod(IResourceRouter::DELETE));
        Assert::true($this->route->isMethod(IResourceRouter::CRUD));
    }

    public function testOtherRequestMethods(): void
    {
        Assert::false($this->route->isMethod(IResourceRouter::HEAD));
        Assert::false($this->route->isMethod(IResourceRouter::PATCH));
    }

    public function testActionDictionary(): void
    {
        $array = $this->route->getActionDictionary();
        Assert::equal(count($array), 4);
        Assert::equal($array[IResourceRouter::GET], 'read');
    }

    public function testDoesNotMatchMask(): void
    {
        $this->httpRequest->expects('getUrl')
            ->once()
            ->andReturn($this->createRequestUrlMock('resources/path'));

        $appRequest = $this->route->match($this->httpRequest);
        Assert::null($appRequest);
    }

    public function testDoesNotMatchRequestMethod(): void
    {
        $this->setupRequestMock();
        $this->httpRequest->expects('getMethod')
            ->atLeastOnce()
            ->andReturn(IRequest::HEAD);

        $this->httpRequest->expects('getHeader')
            ->once()
            ->andReturn(NULL);

        $appRequest = $this->route->match($this->httpRequest);
        Assert::null($appRequest);
    }

    public function testMatchRoute(): void
    {
        $this->setupRequestMock();
        $this->httpRequest->expects('getMethod')
            ->once()
            ->andReturn(IRequest::GET);

        $this->httpRequest->expects('getHeader')
            ->once()
            ->andReturn(NULL);

        $appRequest = $this->route->match($this->httpRequest);
        $params = $appRequest->getParameters();
        Assert::true($appRequest instanceof Nette\Application\Request);
        Assert::equal($params['action'], 'read');
    }

    public function testOverrideRequestMethod(): void
    {
        $this->setupRequestMock();
        $this->httpRequest->expects('getMethod')
            ->once()
            ->andReturn(IRequest::GET);

        $appRequest = $this->route->match($this->httpRequest);
        $params = $appRequest->getParameters();
        Assert::true($appRequest instanceof Nette\Application\Request);
        Assert::equal($params['action'], 'read');
    }

    /**
     * @return void
     */
    private function setupRequestMock(): void
    {
        $this->httpRequest->expects('getUrl')
            ->once()
            ->andReturn($this->createRequestUrlMock());

        $this->httpRequest->expects('getQuery')->once()->andReturn(array());
        $this->httpRequest->expects('getPost')->once()->andReturn(array());
        $this->httpRequest->expects('getFiles')->once()->andReturn(array());
        $this->httpRequest->expects('isSecured')->once()->andReturn(FALSE);
    }

    /**
     * @param string $path
     * @return MockInterface
     */
    private function createRequestUrlMock($path = 'resources/test')
    {
        $url = $this->mockista->create(\Nette\Http\Url::class);
        $url->expects('getHost')->once()->andReturn('host.test');
        $url->expects('getPath')->once()->andReturn($path);
        $url->expects('getBasePath')->once()->andReturn('');
        return $url;
    }
}

(new ResourceRouteTest())->run();
