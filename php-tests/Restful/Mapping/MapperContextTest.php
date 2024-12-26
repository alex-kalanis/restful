<?php

namespace Tests\Restful\Mapping;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\IResource;
use kalanis\Restful\Mapping;
use Tester\Assert;
use Tests\TestCase;


class MapperContextTest extends TestCase
{

    private Mapping\JsonMapper $json;

    private Mapping\XmlMapper $xml;

    private Mapping\MapperContext $context;

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
        }, \kalanis\Restful\Exceptions\InvalidStateException::class);
    }

    public function testGetMapperFromFullContentTypeSpecification(): void
    {
        $mapper = $this->context->getMapper('application/json; charset=utf8');
        Assert::same($mapper, $this->json);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->json = new Mapping\JsonMapper();
        $this->xml = new Mapping\XmlMapper();

        $this->context = new Mapping\MapperContext();
        $this->context->addMapper(IResource::JSON, $this->json);
    }
}


(new MapperContextTest())->run();
