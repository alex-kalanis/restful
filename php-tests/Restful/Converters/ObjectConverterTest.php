<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use ArrayIterator;
use kalanis\Restful\Converters\ObjectConverter;
use kalanis\Restful\Resource\Link;
use Tester\Assert;
use Tests\TestCase;


class ObjectConverterTest extends TestCase
{

    private ObjectConverter $converter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = new ObjectConverter;
    }

    public function testConvertStdClassToArray(): void
    {
        $expected = [
            'stdClass' => [
                'hello' => 'world'
            ]
        ];
        $data = [
            'stdClass' => (object) ['hello' => 'world']
        ];

        $result = $this->converter->convert($data);
        Assert::equal($result, $expected);
    }

    public function testConvertTraversableObjectToArrayWithKeys(): void
    {
        $expected = [
            'traversable' => [
                'hello' => 'world'
            ]
        ];

        $collection = new ArrayIterator([
            'hello' => 'world'
        ]);
        $data = [
            'traversable' => $collection
        ];

        $result = $this->converter->convert($data);
        Assert::same($result, $expected);
    }

    public function testConvertsResourceElementToAnArray(): void
    {
        $expected = [
            ['href' => 'http://resource', 'rel' => 'self']
        ];
        $data = [];
        $data[] = new Link('http://resource');
        $result = $this->converter->convert($data);
        Assert::same($result, $expected);
    }
}


(new ObjectConverterTest())->run();
