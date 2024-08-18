<?php

namespace Tests\Picabo\Restful;

require_once __DIR__ . '/../../bootstrap.php';

use Mockista\MockInterface;
use Picabo\Restful\ConvertedResource;
use Picabo\Restful\Converters\ResourceConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\ConvertedResource.
 *
 * @testCase Tests\Picabo\Restful\ConvertedResourceTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful
 */
class ConvertedResourceTest extends TestCase
{

    /** @var array */
    private $data;

    /** @var MockInterface */
    private $resourceConverter;

    /** @var ConvertedResource */
    private $resource;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = ['I really_do not_like_WhenPeople do not_comply WithStandards' => 'Hello'];
        $this->resourceConverter = $this->mockista->create(ResourceConverter::class);
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
