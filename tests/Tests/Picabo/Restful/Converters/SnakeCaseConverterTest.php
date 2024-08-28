<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Converters\SnakeCaseConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\SnakeCaseConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\SnakeCaseConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class SnakeCaseConverterTest extends TestCase
{

    private SnakeCaseConverter $converter;

    public function testConvertsArrayKeysToSnakeCase(): void
    {
        $data = array(
            'camelCase' => 'is not so good to read'
        );

        $result = $this->converter->convert($data);
        $keys = array_keys($result);
        Assert::same('camel_case', $keys[0]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new SnakeCaseConverter();
    }


}

(new SnakeCaseConverterTest())->run();
