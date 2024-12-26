<?php

namespace Tests\Restful\Converters;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use DateTime;
use kalanis\Restful\Converters\DateTimeConverter;
use Tester\Assert;
use Tests\TestCase;


class DateTimeConverterTest extends TestCase
{

    private DateTimeConverter $converter;

    public function testConvertDateTimeObjectsToString(): void
    {
        $data = [
            [
                'date' => new DateTime('19.1.1996'),
                'modified' => new DateTime('19.1.1996'),
            ]
        ];

        $data = $this->converter->convert($data);
        Assert::equal($data[0]['date'], '1996-01-19T00:00:00+01:00');
        Assert::equal($data[0]['modified'], '1996-01-19T00:00:00+01:00');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->converter = new DateTimeConverter('c');
    }
}


(new DateTimeConverterTest())->run();
