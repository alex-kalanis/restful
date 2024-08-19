<?php

namespace Tests\Picabo\Restful;

require_once __DIR__ . '/../../bootstrap.php';

use Mockista\MockInterface;
use Nette\Http\IRequest;
use Picabo\Restful\Converters\ResourceConverter;
use Picabo\Restful\IResource;
use Picabo\Restful\ResourceFactory;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\ResourceFactory.
 *
 * @testCase Tests\Picabo\Restful\ResourceFactoryTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful
 */
class ResourceFactoryTest extends TestCase
{

    /** @var MockInterface */
    private $request;

    /** @var MockInterface */
    private $resourceConverter;

    /** @var ResourceFactory */
    private $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->mockista->create(IRequest::class);
        $this->resourceConverter = $this->mockista->create(ResourceConverter::class);
        $this->factory = new ResourceFactory($this->request, $this->resourceConverter);
    }

    public function testCreateResource(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('application/json');

        $resource = $this->factory->create();
        Assert::true($resource instanceof IResource);
    }

    public function testCreateResourceWithDefaultData(): void
    {
        $data = ['test' => 'factory'];

        $this->request->expects('getHeader')
            ->once()
            ->with('Accept')
            ->andReturn('application/json');
        $this->resourceConverter->expects('convert')
            ->once()
            ->with($data)
            ->andReturn($data);

        $resource = $this->factory->create($data);
        Assert::true($resource instanceof IResource);
        Assert::same($resource->getData(), $data);
    }

}

(new ResourceFactoryTest())->run();