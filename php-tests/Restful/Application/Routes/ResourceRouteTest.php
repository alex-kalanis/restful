<?php

namespace Tests\Restful\Application\Routes;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\IResourceRouter;
use kalanis\Restful\Application\Routes\ResourceRoute;
use Mockery;
use Nette\Http\IRequest;
use Tester\Assert;
use Tests\TestCase;


class ResourceRouteTest extends TestCase
{

    private ResourceRoute $route;

    private $httpRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->route = new ResourceRoute('resources/test', [
            'module' => 'Resources',
            'presenter' => 'Test',
            'action' => [
                IResourceRouter::GET => 'read',
                IResourceRouter::POST => 'create',
                IResourceRouter::PUT => 'update',
                IResourceRouter::DELETE => 'delete',
            ]
        ], IResourceRouter::CRUD);
        $this->httpRequest = Mockery::mock(\Nette\Http\IRequest::class);
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
            ->atLeast()
            ->once()
            ->andReturn(IRequest::Head);

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
            ->andReturn(IRequest::Get);

        $this->httpRequest->expects('getHeader')
            ->once()
            ->andReturn(NULL);

        $params = $this->route->match($this->httpRequest);
        Assert::equal($params['action'], 'read');
    }

    public function testOverrideRequestMethod(): void
    {
        $this->setupRequestMock();
        $this->httpRequest->expects('getMethod')
            ->once()
            ->andReturn(IRequest::Get);

        $params = $this->route->match($this->httpRequest);
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

        $this->httpRequest->expects('getQuery')->once()->andReturn([]);
        $this->httpRequest->expects('getPost')->once()->andReturn([]);
        $this->httpRequest->expects('getFiles')->once()->andReturn([]);
        $this->httpRequest->expects('isSecured')->once()->andReturn(FALSE);
    }

    private function createRequestUrlMock($path = 'resources/test')
    {
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $url->expects('getHost')->once()->andReturn('host.test');
        $url->expects('getPath')->once()->andReturn($path);
        $url->expects('getBasePath')->once()->andReturn('');
        return $url;
    }
}


(new ResourceRouteTest())->run();
