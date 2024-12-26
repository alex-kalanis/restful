<?php

namespace Tests\Restful\Http;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Http\ApiRequestFactory;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class ApiRequestFactoryTest extends TestCase
{

    private ApiRequestFactory $apiRequestFactory;

    private $requestFactory;

    private $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createRequestMock();
        $this->requestFactory = Mockery::mock(\Nette\Http\RequestFactory::class);
        $this->requestFactory->expects('fromGlobals')->andReturn($this->request);
        $this->apiRequestFactory = new ApiRequestFactory($this->requestFactory);
    }

    private function createRequestMock()
    {
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $url->expects('__get')->once()->with('query')->andReturn('');
        $url->expects('setQuery')->once();
        $url->expects('withQuery')->andReturnSelf();

        $request = Mockery::mock(\Nette\Http\Request::class);
        $request->expects('getUrl')->once()->andReturn($url);
        $request->expects('getQuery')->once()->andReturn('');
        $request->expects('getPost')->once()->andReturn([]);
        $request->expects('getFile')->once()->andReturn(null);
        $request->expects('getFiles')->once()->andReturn([]);
        $request->expects('getCookie')->once()->andReturn(NULL);
        $request->expects('getCookies')->once()->andReturn([]);
        $request->expects('getHeaders')->once()->andReturn([]);
        $request->expects('getRemoteAddress')->once()->andReturn(NULL);
        $request->expects('getRemoteHost')->once()->andReturn(NULL);
        return $request;
    }

    public function testCreatesRequestWithMethodServerWasRequested(): void
    {
        $this->request->expects('getHeader')
            ->with(ApiRequestFactory::OVERRIDE_HEADER)
            ->andReturn(NULL);

        $this->request->expects('getQuery')
            ->with(ApiRequestFactory::OVERRIDE_PARAM)
            ->andReturn(NULL);

        $this->request->expects('getMethod')
            ->once()
            ->andReturn('GET');

        $request = $this->apiRequestFactory->createHttpRequest();
        Assert::equal($request->getMethod(), 'GET');
    }

    public function testCreatesRequestWithMethodThatIsInOverrideHeader(): void
    {
        $this->request->expects('getMethod')
            ->once()
            ->andReturn('POST');

        $this->request->expects('getHeader')
            ->with(ApiRequestFactory::OVERRIDE_HEADER)
            ->andReturn('DELETE');

        $request = $this->apiRequestFactory->createHttpRequest();
        Assert::equal($request->getMethod(), 'DELETE');
    }

    public function testCreateRequestWithMethodThatIsInQueryParameter(): void
    {
        $this->request->expects('getMethod')
            ->once()
            ->andReturn('POST');

        $this->request->expects('getHeader')
            ->with(ApiRequestFactory::OVERRIDE_HEADER)
            ->andReturn(NULL);

        $this->request->expects('getQuery')
            ->with(ApiRequestFactory::OVERRIDE_PARAM)
            ->andReturn('DELETE');

        $request = $this->apiRequestFactory->createHttpRequest();
        Assert::equal($request->getMethod(), 'DELETE');
    }

    public function testDoesNotOverrideMethodWithHeaderIfRequestedWithGetMethod(): void
    {
        $this->request->expects('getMethod')
            ->once()
            ->andReturn('GET');

        $this->request->expects('getHeader')
            ->with(ApiRequestFactory::OVERRIDE_HEADER)
            ->andReturn('DELETE');

        $this->request->expects('getQuery')
            ->with(ApiRequestFactory::OVERRIDE_PARAM)
            ->andReturn(NULL);

        $request = $this->apiRequestFactory->createHttpRequest();
        Assert::equal($request->getMethod(), 'GET');
    }

    public function testDoesNotOverrideMethodWithQueryParameterIfRequestedWithGetMethod(): void
    {
        $this->request->expects('getMethod')
            ->once()
            ->andReturn('GET');

        $this->request->expects('getHeader')
            ->with(ApiRequestFactory::OVERRIDE_HEADER)
            ->andReturn(NULL);

        $this->request->expects('getQuery')
            ->with(ApiRequestFactory::OVERRIDE_PARAM)
            ->andReturn('DELETE');

        $request = $this->apiRequestFactory->createHttpRequest();
        Assert::equal($request->getMethod(), 'GET');
    }
}


(new ApiRequestFactoryTest())->run();
