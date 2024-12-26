<?php

namespace Tests\Restful\Http;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Exceptions\InvalidStateException;
use kalanis\Restful\Http\IInput;
use kalanis\Restful\Http\InputFactory;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class InputFactoryTest extends TestCase
{

    private $request;

    private $mapperContext;

    private $validationScopeFactory;

    private InputFactory $inputFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->request->expects('getRawBody')->once();
        $this->mapperContext = Mockery::mock(\kalanis\Restful\Mapping\MapperContext::class);
        $this->validationScopeFactory = Mockery::mock(\kalanis\Restful\Validation\IValidationScopeFactory::class);
        $this->inputFactory = new InputFactory($this->request, $this->mapperContext, $this->validationScopeFactory);
    }

    public function testCreateInputWithMixedPostAndQueryData(): void
    {
        $post = ['post' => 'data', 'same' => 'POST'];
        $query = ['get' => 'data', 'same' => 'GET'];
        $exception = new InvalidStateException;

        $expected = [
            'get' => 'data',
            'same' => 'POST',
            'post' => 'data',
        ];

        $this->request->expects('getPost')
            ->atLeast()
            ->once()
            ->andReturn($post);
        $this->request->expects('getQuery')
            ->atLeast()
            ->once()
            ->andReturn($query);
        $this->request->expects('getHeader')
            ->atLeast()
            ->once()
            ->with('Content-Type')
            ->andReturn('application/test');

        $this->mapperContext->expects('getMapper')
            ->once()
            ->with('application/test')
            ->andThrow($exception);

        $input = $this->inputFactory->create();
        Assert::true($input instanceof IInput);
        Assert::same($input->getData(), $expected);
    }
}


(new InputFactoryTest())->run();
