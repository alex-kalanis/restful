<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Converters\CamelCaseConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\CamelCaseConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\CamelCaseConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class CamelCaseConverterTest extends TestCase
{

    private CamelCaseConverter $converter;

    public function testConvertsArrayKeysToCamelCase(): void
    {
        $data = array(
            'nice array-key' => 'value'
        );

        $result = $this->converter->convert($data);
        $keys = array_keys($result);
        Assert::same('niceArrayKey', $keys[0]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new CamelCaseConverter();
    }

}

(new CamelCaseConverterTest())->run();
