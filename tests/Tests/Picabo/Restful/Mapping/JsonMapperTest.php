<?php

namespace Tests\Picabo\Restful\Mapping;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Mapping\IMapper;
use Picabo\Restful\Mapping\JsonMapper;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Mapping\JsonMapper.
 *
 * @testCase Tests\Picabo\Restful\Mapping\JsonMapperTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Mapping
 */
class JsonMapperTest extends TestCase
{

    /** @var IMapper */
    private $mapper;

    public function testConvertArrayToJson(): void
    {
        $array = array('node' => 'value');
        $json = $this->mapper->stringify($array, FALSE);
        Assert::equal($json, '{"node":"value"}');
    }

    public function testConvertArrayToJsonWithPrettyPrint(): void
    {
        $array = array('node' => 'value');
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
        }, \Picabo\Restful\Mapping\Exceptions\MappingException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new JsonMapper;
    }

}

(new JsonMapperTest())->run();
