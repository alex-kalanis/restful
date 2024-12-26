<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Converters\SnakeCaseConverter;
use Tester\Assert;
use Tests\TestCase;


class SnakeCaseConverterTest extends TestCase
{

    private SnakeCaseConverter $converter;

    public function testConvertsArrayKeysToSnakeCase(): void
    {
        $data = [
            'camelCase' => 'is not so good to read'
        ];

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
