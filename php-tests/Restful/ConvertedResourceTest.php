<?php

namespace Tests\Restful;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\ConvertedResource;
use kalanis\Restful\Converters\ResourceConverter;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class ConvertedResourceTest extends TestCase
{

    private array $data;

    private $resourceConverter;

    private ConvertedResource $resource;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = ['I really_do not_like_WhenPeople do not_comply WithStandards' => 'Hello'];
        $this->resourceConverter = Mockery::mock(ResourceConverter::class);
        $this->resource = new ConvertedResource($this->resourceConverter, $this->data);
    }

    public function testGetConvertedDataUsingResourceConverter(): void
    {
        $converted = ['i_really_do_not_like__when_people_do_not_comply__with_standards' => 'Hello'];

        $this->resourceConverter->expects('convert')
            ->once()
            ->with($this->data)
            ->andReturn($converted);

        $data = $this->resource->getData();
        Assert::equal($data, $converted);
    }
}


(new ConvertedResourceTest())->run();
