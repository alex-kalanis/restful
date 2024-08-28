<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Converters\PascalCaseConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\PascalCaseConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\PascalCaseConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class PascalCaseConverterTest extends TestCase
{

    private PascalCaseConverter $converter;

    public function testConvertsArrayKeysToCamelCase(): void
    {
        $data = array(
            'nice array-key' => 'value'
        );

        $result = $this->converter->convert($data);
        $keys = array_keys($result);
        Assert::same('NiceArrayKey', $keys[0]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new PascalCaseConverter();
    }

}

(new PascalCaseConverterTest())->run();
