<?php

namespace Tests\Picabo\Restful\Http;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockista\MockInterface;
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

    /** @var MockInterface */
    private $request;

    /** @var MockInterface */
    private $mapperContext;

    /** @var MockInterface */
    private $validationScopeFactory;

    /** @var InputFactory */
    private $inputFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->mockista->create(\Nette\Http\IRequest::class);
        $this->request->expects('getRawBody')->once();
        $this->mapperContext = $this->mockista->create(\Picabo\Restful\Mapping\MapperContext::class);
        $this->validationScopeFactory = $this->mockista->create(\Picabo\Restful\Validation\IValidationScopeFactory::class);
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
            ->atLeastOnce()
            ->andReturn($post);
        $this->request->expects('getQuery')
            ->atLeastOnce()
            ->andReturn($query);
        $this->request->expects('getHeader')
            ->atLeastOnce()
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
