<?php

namespace Tests\Restful\Utils;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Utils\Strings;
use Tester\Assert;
use Tests\TestCase;


class StringsTest extends TestCase
{

    public function testConvertsFirstLetterToLowerCase(): void
    {
        $string = Strings::firstLower('LOWER');
        Assert::equal($string, 'lOWER');
    }

    public function testConvertsStringToCamelCase(): void
    {
        $camel = Strings::toCamelCase('I really_do not_like_WhenPeople do not_comply WithStandards');
        Assert::equal($camel, 'iReallyDoNotLikeWhenPeopleDoNotComplyWithStandards');
    }

    public function testConvertsStringToSnakeCase(): void
    {
        $snake = Strings::toSnakeCase('I really_do not_like_WhenPeople do not_comply WithStandards');
        Assert::equal($snake, 'i_really_do_not_like_when_people_do_not_comply__with_standards');
    }

    public function testConvertsStringToPascalCase(): void
    {
        $pascal = Strings::toPascalCase('I really_do not_like_WhenPeople do not_comply WithStandards');
        Assert::equal($pascal, 'IReallyDoNotLikeWhenPeopleDoNotComplyWithStandards');
    }
}


(new StringsTest())->run();
