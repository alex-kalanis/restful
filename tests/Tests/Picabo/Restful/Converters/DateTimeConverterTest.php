<?php

namespace Tests\Picabo\Restful\Converters;

require_once __DIR__ . '/../../../bootstrap.php';

use DateTime;
use Picabo\Restful\Converters\DateTimeConverter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Converters\DateTimeConverter.
 *
 * @testCase Tests\Picabo\Restful\Converters\DateTimeConverterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Converters
 */
class DateTimeConverterTest extends TestCase
{

    /** @var DateTimeConverter */
    private $converter;

    public function testConvertDateTimeObjectsToString(): void
    {
        $data = array(
            array(
                'date' => new DateTime('19.1.1996'),
                'modified' => new DateTime('19.1.1996'),
            )
        );

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
