<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Converters\PascalCaseConverter;
use Tester\Assert;
use Tests\TestCase;


class PascalCaseConverterTest extends TestCase
{

    private PascalCaseConverter $converter;

    public function testConvertsArrayKeysToCamelCase(): void
    {
        $data = [
            'nice array-key' => 'value'
        ];

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
