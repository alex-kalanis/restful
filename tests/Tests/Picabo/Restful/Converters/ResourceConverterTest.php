<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Converters\ResourceConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\ResourceConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\ResourceConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class ResourceConverterTest extends TestCase
{

    private $converter;

    private ResourceConverter $resourceConverter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = Mockery::mock(\Picabo\Restful\Converters\IConverter::class);
        $this->resourceConverter = new ResourceConverter;
    }

    public function testConvertsResourceUsingGivenConverters(): void
    {
        $data = array('test' => 'resource');
        $converted = array('test' => 'resource converted');

        $this->resourceConverter->addConverter($this->converter);
        $this->converter->expects('convert')
            ->once()
            ->with($data)
            ->andReturn($converted);

        $result = $this->resourceConverter->convert($data);
        Assert::same($result, $converted);
    }

}

(new ResourceConverterTest())->run();
