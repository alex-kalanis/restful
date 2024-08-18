<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockista\MockInterface;
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

    /** @var MockInterface */
    private $converter;

    /** @var ResourceConverter */
    private $resourceConverter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = $this->mockista->create('Picabo\Restful\Converters\IConverter');
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
