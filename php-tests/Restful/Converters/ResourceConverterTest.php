<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Converters\ResourceConverter;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class ResourceConverterTest extends TestCase
{

    private $converter;

    private ResourceConverter $resourceConverter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = Mockery::mock(\kalanis\Restful\Converters\IConverter::class);
        $this->resourceConverter = new ResourceConverter;
    }

    public function testConvertsResourceUsingGivenConverters(): void
    {
        $data = ['test' => 'resource'];
        $converted = ['test' => 'resource converted'];

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
