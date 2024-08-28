<?php

namespace Tests\Picabo\Restful\Http;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Http\InputFactory;
use Picabo\Restful\Exceptions\InvalidStateException;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Http\InputFactory.
 *
 * @testCase Tests\Picabo\Restful\Http\InputFactoryTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Http
 */
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
        $this->mapperContext = Mockery::mock(\Picabo\Restful\Mapping\MapperContext::class);
        $this->validationScopeFactory = Mockery::mock(\Picabo\Restful\Validation\IValidationScopeFactory::class);
        $this->inputFactory = new InputFactory($this->request, $this->mapperContext, $this->validationScopeFactory);
    }

    public function testCreateInputWithMixedPostAndQueryData(): void
    {
        $post = array('post' => 'data', 'same' => 'POST');
        $query = array('get' => 'data', 'same' => 'GET');
        $exception = new InvalidStateException;

        $expected = array(
            'get' => 'data',
            'same' => 'POST',
            'post' => 'data'
        );

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
