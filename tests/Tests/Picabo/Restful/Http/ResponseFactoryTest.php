<?php

namespace Tests\Picabo\Restful\Http;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Nette;
use Nette\Http\IResponse;
use Picabo\Restful\Http\ResponseFactory;
use Picabo\Restful\Exceptions\InvalidStateException;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Http\ResponseFactory.
 *
 * @testCase Tests\Picabo\Restful\Http\ResponseFactoryTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Http
 */
class ResponseFactoryTest extends TestCase
{

    private ResponseFactory $factory;

    private $request;

    private $response;

    private $filter;

    private $url;

    public function setUp(): void
    {
        parent::setUp();
        $this->filter = Mockery::mock(\Picabo\Restful\Utils\RequestFilter::class);
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->response = Mockery::mock(\Nette\Http\IResponse::class);
        $this->url = new Nette\Http\UrlScript('http://resource/');
        $this->factory = new ResponseFactory($this->request, $this->filter);
        $this->factory->setResponse($this->response);
    }

    public function testCreateHttpResponseWithGivenStatusCode(): void
    {
        $exception = new InvalidStateException;
        $this->filter->expects('getPaginator')
            ->once()
            ->andThrow($exception);

        $this->response->expects('setCode')
            ->once()
            ->with(422);

        $this->request->expects('getUrl')->once()->andReturn($this->url);
        $this->response->expects('setHeader')->once()->with('Allow', '');

        $response = $this->factory->createHttpResponse(422);
        Assert::true($response instanceof IResponse);
        Assert::same($response, $this->response);
    }

    public function testCreateHttpResponseWithPaginator(): void
    {
        $paginator = $this->createPaginatorMock();
        $this->filter->expects('getPaginator')
            ->once()
            ->andReturn($paginator);

        $this->response->expects('setCode')
            ->once()
            ->with(200);

        $this->request->expects('getUrl')->twice()->andReturn($this->url);

        $this->response->expects('setHeader')
            ->once()
            ->with('Link', '<http://resource/?offset=10&limit=10>;rel="next", <http://resource/?offset=90&limit=10>;rel="last"');
        $this->response->expects('setHeader')
            ->once()
            ->with('X-Total-Count', 100);

        $response = $this->factory->createHttpResponse(200);
        Assert::true($response instanceof IResponse);
    }

    /**
     * Create paginator mock
     */
    private function createPaginatorMock()
    {
        $paginator = Mockery::mock(\Nette\Utils\Paginator::class);
        $paginator->expects('getPage')->atLeast()->once()->andReturn(1);
        $paginator->expects('getLastPage')->atLeast()->once()->andReturn(10);
        $paginator->expects('getItemsPerPage')->atLeast()->once()->andReturn(10);
        $paginator->expects('getOffset')->atLeast()->once()->andReturn(10);
        $paginator->expects('getItemCount')->atLeast()->once()->andReturn(100);
        $paginator->expects('setPage')->atLeast()->once();
        return $paginator;
    }

    public function testCreateHttpResponseWithAllowedMethods(): void
    {
        $exception = new InvalidStateException;
        $this->filter->expects('getPaginator')
            ->once()
            ->andThrow($exception);

        $this->response->expects('setCode')
            ->once()
            ->with(200);

        $this->request->expects('getUrl')->once()->andReturn($this->url);

        $response = $this->factory->createHttpResponse(200);
        Assert::true($response instanceof IResponse);
        Assert::same($response, $this->response);
    }

    public function testCreateHttpResponseWithDefaultStatusCodeDeterminedFromRequestMethod(): void
    {
        $exception = new InvalidStateException;
        $this->filter->expects('getPaginator')
            ->once()
            ->andThrow($exception);

        $this->response->expects('setCode')
            ->once()
            ->with(201);

        $this->request->expects('getMethod')->once()->andReturn('POST');
        $this->request->expects('getUrl')->once()->andReturn($this->url);

        $response = $this->factory->createHttpResponse();
        Assert::true($response instanceof IResponse);
        Assert::same($response, $this->response);
    }

}

(new ResponseFactoryTest())->run();
