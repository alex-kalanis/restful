<?php

namespace Tests\Restful\Mapping;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Mapping\IMapper;
use kalanis\Restful\Mapping\JsonMapper;
use Tester;
use Tester\Assert;
use Tests\TestCase;


class JsonMapperTest extends TestCase
{

    private IMapper $mapper;

    public function testConvertArrayToJson(): void
    {
        $array = ['node' => 'value'];
        $json = $this->mapper->stringify($array, FALSE);
        Assert::equal($json, '{"node":"value"}');
    }

    public function testConvertArrayToJsonWithPrettyPrint(): void
    {
        $array = ['node' => 'value'];
        $json = $this->mapper->stringify($array);
        if (!defined('Nette\\Utils\\Json::PRETTY')) {
            Tester\Environment::skip('Json does not support PRETTY PRINT in this Nette version');
        }
        Assert::equal($json, "{\n    \"node\": \"value\"\n}");
    }

    public function testConvertJsonToArray(): void
    {
        $array = $this->mapper->parse('{"node":"value"}');
        Assert::equal($array['node'], 'value');
    }

    public function testConvertsJsonRecursivelyToArray(): void
    {
        $array = $this->mapper->parse('{"user":{"name":"test","phone":500}}');
        Assert::equal($array['user']['name'], 'test');
    }

    public function testThrowsExceptionWhenJsonIsInvalid(): void
    {
        Assert::throws(function () {
            $this->mapper->parse('{"node: "invalid JSON"}');
        }, \kalanis\Restful\Mapping\Exceptions\MappingException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new JsonMapper;
    }
}


(new JsonMapperTest())->run();
