<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Converters\CamelCaseConverter;
use Tester\Assert;
use Tests\TestCase;


class CamelCaseConverterTest extends TestCase
{

    private CamelCaseConverter $converter;

    public function testConvertsArrayKeysToCamelCase(): void
    {
        $data = [
            'nice array-key' => 'value'
        ];

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
