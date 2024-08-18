<?php

namespace Tests\Picabo\Restful\Mapping;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockista\MockInterface;
use Picabo\Restful\IResource;
use Picabo\Restful\Mapping\MapperContext;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Mapping\MapperContext.
 *
 * @testCase Tests\Picabo\Restful\Mapping\MapperContextTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Mapping
 */
class MapperContextTest extends TestCase
{

    /** @var MockInterface */
    private $json;

    /** @var MockInterface */
    private $xml;

    /** @var MapperContext */
    private $context;

    public function testSelectMapperByContentType(): void
    {
        $this->context->addMapper(IResource::XML, $this->xml);
        $mapper = $this->context->getMapper(IResource::XML);

        Assert::same($mapper, $this->xml);
    }

    public function testThrowsExceptionWhenContentTypeIsUnknown(): void
    {
        Assert::throws(function () {
            $this->context->getMapper(IResource::DATA_URL);
        }, \Picabo\Restful\Exceptions\InvalidStateException::class);
    }

    public function testGetMapperFromFullContentTypeSpecification(): void
    {
        $mapper = $this->context->getMapper('application/json; charset=utf8');
        Assert::same($mapper, $this->json);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->json = $this->mockista->create(\Picabo\Restful\Mapping\JsonMapper::class);
        $this->xml = $this->mockista->create(\Picabo\Restful\Mapping\XmlMapper::class);

        $this->context = new MapperContext;
        $this->context->addMapper(IResource::JSON, $this->json);
    }

}

(new MapperContextTest())->run();
