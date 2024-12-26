<?php

namespace Tests\Restful\Application;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\ResponseFactory;
use kalanis\Restful\Application\Responses\JsonpResponse;
use kalanis\Restful\Application\Responses\TextResponse;
use kalanis\Restful\IResource;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class ResponseFactoryTest extends TestCase
{
    private ResponseFactory $factory;

    private $resource;

    private $mapper;

    private $request;

    private $response;

    private $mapperContext;

    public function testCreateResponse(): void
    {
        $this->response->expects('setCode')
            ->once()
            ->with(204);

        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn(IResource::JSON);

        $this->resource->expects('getData')
            ->once()
            ->andReturn([]);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with(IResource::JSON)
            ->andReturn($this->mapper);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(FALSE);

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        $response = $this->factory->create($this->resource);
        Assert::true($response instanceof TextResponse);
    }

    public function testCreateCustomResponse(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('text');

        $this->resource->expects('getData')
            ->once()
            ->andReturn(['test']);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with('text')
            ->andReturn($this->mapper);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(FALSE);

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        $this->factory->registerResponse('text', TextResponse::class);
        $response = $this->factory->create($this->resource);

        Assert::true($response instanceof TextResponse);
    }

    public function testCreateJsonpResponseWhenJsonpIsActive(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('text');

        $this->resource->expects('getData')
            ->once()
            ->andReturn(['test']);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn('callback');

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with(IResource::JSONP)
            ->andReturn($this->mapper);

        $this->factory->registerResponse('text', \Nette\Application\Responses\TextResponse::class);
        $response = $this->factory->create($this->resource);

        Assert::true($response instanceof JsonpResponse);
    }

    public function testThrowsExceptionWhenResponseTypeIsNotFound(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('kalanis/test');

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(FALSE);

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        Assert::throws(function () {
            $this->factory->create($this->resource);
        }, \kalanis\Restful\Exceptions\InvalidStateException::class);
    }

    public function testThrowsExceptionWhenResponseClassNotExists(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(FALSE);

        $factory = $this->factory;
        Assert::throws(function () use ($factory) {
            $factory->registerResponse('test/plain', '\kalanis\TestResponse');
        }, \kalanis\Restful\Exceptions\InvalidArgumentException::class);
    }

    public function testCreateResponseBasedOnRequestedContentTypeIfJsonpIsDisabled(): void
    {
        $this->factory->setJsonp(FALSE);

        $this->response->expects('setCode')
            ->once()
            ->with(204);

        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn(IResource::JSON);

        $this->resource->expects('getData')
            ->once()
            ->andReturn([]);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with(IResource::JSON)
            ->andReturn($this->mapper);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn('callback');

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        $this->request->expects('getQuery')
            ->once()
            ->with('')
            ->andReturn(null);

        $response = $this->factory->create($this->resource);
        Assert::true($response instanceof TextResponse);
    }

    public function testSelectsFirstContentTypeIfAcceptHeaderAcceptsAll(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('*/*');

        $this->resource->expects('getData')
            ->once()
            ->andReturn(['test']);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn([]);

        $this->request->expects('getQuery')
            ->once()
            ->with('prettyPrint')
            ->andReturn(FALSE);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with(IResource::JSON)
            ->andReturn($this->mapper);

        $response = $this->factory->create($this->resource);

        Assert::true($response instanceof TextResponse);
    }

    public function testUseCustomPrettyPrintKeyName(): void
    {

        $this->response->expects('setCode')
            ->once()
            ->with(204);

        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn(IResource::JSON);

        $this->resource->expects('getData')
            ->once()
            ->andReturn([]);

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(true);

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with(IResource::JSON)
            ->andReturn($this->mapper);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn('');

        $this->request->expects('getQuery')
            ->once()
            ->with('pretty')
            ->andReturn('');

        $this->factory->setPrettyPrintKey('pretty');
        $response = $this->factory->create($this->resource);

        Assert::true($response instanceof TextResponse);
    }

    public function testAcceptContentTypeIfItsIsRegistered(): void
    {
        Assert::true($this->factory->isAcceptable(IResource::JSON));
    }

    public function testRejectContentTypeIfItsIsNotRegistered(): void
    {
        Assert::false($this->factory->isAcceptable('data/custom'));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = Mockery::mock(\Nette\Http\IResponse::class);
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->mapperContext = Mockery::mock(\kalanis\Restful\Mapping\MapperContext::class);
        $this->factory = new ResponseFactory(
            $this->response,
            $this->request,
            $this->mapperContext,
        );
        $this->factory->setJsonp('jsonp');
        $this->resource = Mockery::mock(\kalanis\Restful\Resource::class);
        $this->mapper = Mockery::mock(\kalanis\Restful\Mapping\IMapper::class);
    }
}


(new ResponseFactoryTest())->run();
