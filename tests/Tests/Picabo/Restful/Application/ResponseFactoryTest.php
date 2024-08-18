<?php

namespace Tests\Picabo\Restful\Application;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Application\ResponseFactory;
use Picabo\Restful\Application\Responses\JsonpResponse;
use Picabo\Restful\Application\Responses\TextResponse;
use Picabo\Restful\IResource;
use Mockista\MockInterface;
use Nette;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\ResponseFactory.
 *
 * @testCase
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application
 */
class ResponseFactoryTest extends TestCase
{
    /** @var ResponseFactory */
    private $factory;

    /** @var MockInterface */
    private $resource;

    /** @var MockInterface */
    private $mapper;

    /** @var MockInterface */
    private $request;

    /** @var MockInterface */
    private $response;

    /** @var MockInterface */
    private $mapperContext;

    /** @var MockInterface */
    private $cacheValidator;

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
            ->andReturn(array());

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

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
            ->andReturn('test');

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

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

        $this->factory->registerResponse('text', \Nette\Application\Responses\TextResponse::class);
        $response = $this->factory->create($this->resource);

        Assert::true($response instanceof Nette\Application\Responses\TextResponse);
    }

    public function testCreateJsonpResponseWhenJsonpIsActive(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('text');

        $this->resource->expects('getData')
            ->once()
            ->andReturn('test');

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

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
            ->andReturn('Picabo/test');

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
        }, \Picabo\Restful\Exceptions\InvalidStateException::class);
    }

    public function testThrowsExceptionWhenResponseClassNotExists(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(FALSE);

        $factory = $this->factory;
        Assert::throws(function () use ($factory) {
            $factory->registerResponse('test/plain', \Picabo\TestResponse::class);
        }, \Picabo\Restful\Exceptions\InvalidArgumentException::class);
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
            ->andReturn(array());

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

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
            ->andReturn('test');

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

        $this->request->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn(NULL);

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
            ->andReturn(array());

        $this->resource->expects('hasData')
            ->once()
            ->andReturn(TRUE);

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
            ->with('pretty')
            ->andReturn(FALSE);

        $this->factory->prettyPrintKey = 'pretty';
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
        $this->response = $this->mockista->create(\Nette\Http\IResponse::class);
        $this->request = $this->mockista->create(\Nette\Http\IRequest::class);
        $this->mapperContext = $this->mockista->create(\Picabo\Restful\Mapping\MapperContext::class);
        $this->factory = new ResponseFactory($this->response, $this->request, $this->mapperContext);
        $this->factory->setJsonp('jsonp');
        $this->resource = $this->mockista->create(\Picabo\Restful\Resource::class);
        $this->mapper = $this->mockista->create(\Picabo\Restful\Mapping\IMapper::class);
    }

}

(new ResponseFactoryTest())->run();
