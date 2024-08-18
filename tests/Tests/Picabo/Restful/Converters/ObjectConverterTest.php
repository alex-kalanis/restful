<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use ArrayIterator;
use Picabo\Restful\Converters\ObjectConverter;
use Picabo\Restful\Resource\Link;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\ObjectConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\ObjectConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class ObjectConverterTest extends TestCase
{

    /** @var ObjectConverter */
    private $converter;

    public function setUp(): void
    {
        parent::setUp();
        $this->converter = new ObjectConverter;
    }

    public function testConvertStdClassToArray(): void
    {
        $expected = array(
            'stdClass' => array(
                'hello' => 'world'
            )
        );
        $data = array(
            'stdClass' => (object)array('hello' => 'world')
        );

        $result = $this->converter->convert($data);
        Assert::equal($result, $expected);
    }

    public function testConvertTraversableObjectToArrayWithKeys(): void
    {
        $expected = array(
            'traversable' => array(
                'hello' => 'world'
            )
        );

        $collection = new ArrayIterator(array(
            'hello' => 'world'
        ));
        $data = array(
            'traversable' => $collection
        );

        $result = $this->converter->convert($data);
        Assert::same($result, $expected);
    }

    public function testConvertsResourceElementToAnArray(): void
    {
        $expected = array(
            array('href' => 'http://resource', 'rel' => 'self')
        );
        $data = array();
        $data[] = new Link('http://resource');
        $result = $this->converter->convert($data);
        Assert::same($result, $expected);
    }

}

(new ObjectConverterTest())->run();
