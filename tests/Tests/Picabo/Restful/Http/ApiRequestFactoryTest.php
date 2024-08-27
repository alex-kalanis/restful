<?php

namespace Tests\Picabo\Restful\Http;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Nette\Http\RequestFactory;
use Picabo\Restful\Http\ApiRequestFactory;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Http\ApiRequestFactory.
 *
 * @testCase Tests\Picabo\Restful\Http\ApiRequestFactoryTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Http
 */
class ApiRequestFactoryTest extends TestCase
{

    /** @var ApiRequestFactory */
    private $apiRequestFactory;

    /** @var RequestFactory */
    private $requestFactory;

    private $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createRequestMock();
        $this->requestFactory = Mockery::mock(\Nette\Http\RequestFactory::class);
        $this->requestFactory->expects('createHttpRequest')->andReturn($this->request);
        $this->apiRequestFactory = new ApiRequestFactory($this->requestFactory);
    }

    private function createRequestMock()
    {
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $url->expects('__get')->once()->with('query')->andReturn('');
        $url->expects('setQuery')->once();

        $request = Mockery::mock(\Nette\Http\IRequest::class);
        $request->expects('getUrl')->once()->andReturn($url);
        $request->expects('getQuery')->once()->andReturn(NULL);
        $request->expects('getPost')->once()->andReturn(NULL);
        $request->expects('getFiles')->once()->andReturn(NULL);
        $request->expects('getCookies')->once()->andReturn(NULL);
        $request->expects('getHeaders')->once()->andReturn(NULL);
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
