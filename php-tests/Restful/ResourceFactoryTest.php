<?php

namespace Tests\Restful;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Converters\ResourceConverter;
use kalanis\Restful\IResource;
use kalanis\Restful\ResourceFactory;
use Mockery;
use Nette\Http\IRequest;
use Tester\Assert;
use Tests\TestCase;


class ResourceFactoryTest extends TestCase
{

    private $request;
    private $resourceConverter;

    private ResourceFactory $factory;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = Mockery::mock(IRequest::class);
        $this->resourceConverter = Mockery::mock(ResourceConverter::class);
        $this->factory = new ResourceFactory($this->resourceConverter);
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
