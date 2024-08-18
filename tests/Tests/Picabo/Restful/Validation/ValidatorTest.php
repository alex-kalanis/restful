<?php

namespace Tests\Picabo\Restful\Validation;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Validation\IValidator;
use Picabo\Restful\Validation\Rule;
use Picabo\Restful\Validation\Validator;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Validation\Validator.
 *
 * @testCase Tests\Picabo\Restful\Validation\ValidatorTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Validation
 */
class ValidatorTest extends TestCase
{

    /** @var Rule */
    private $rule;

    /** @var Validator */
    private $validator;

    public function testValidateRegularExpression(): void
    {
        $this->rule->expression = IValidator::REGEXP;
        $this->rule->argument = "/[a-z0-9]*/i";
        Assert::true($this->validator->validate('05das', $this->rule));
    }

    public function testThrowsExceptionWhenRegularExpressionNotMatch(): void
    {
        $this->rule->expression = IValidator::REGEXP;
        $this->rule->argument = '/[a-z0-9]{5}/i';
        Assert::throws(function () {
            $this->validator->validate('05_as', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testThrowsExceptionWhenRegularExpressionIsNotGiven(): void
    {
        $this->rule->expression = IValidator::REGEXP;
        $this->rule->argument = NULL;
        Assert::throws(function () {
            $this->validator->validate('05_as', $this->rule);
        }, \Picabo\Restful\Exceptions\InvalidArgumentException::class);
    }

    public function testValidateEqualExpression(): void
    {
        $this->rule->expression = IValidator::EQUAL;
        $this->rule->argument = 10;
        Assert::true($this->validator->validate('10', $this->rule));
    }

    public function testThrowsExceptionWhenValuesAreNotSame(): void
    {
        $this->rule->expression = IValidator::EQUAL;
        $this->rule->argument = 10;
        Assert::throws(function () {
            $this->validator->validate('5', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateEmailExpression(): void
    {
        $this->rule->expression = IValidator::EMAIL;
        Assert::true($this->validator->validate('test@domain.com', $this->rule));
    }

    public function testThrowsExceptionWhenEmailIsInvalid(): void
    {
        $this->rule->expression = IValidator::EMAIL;
        Assert::throws(function () {
            $this->validator->validate('invalid', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateUrl(): void
    {
        $this->rule->expression = IValidator::URL;
        Assert::true($this->validator->validate('http://www.domain.com', $this->rule));
    }

    public function testThrowsExceptionWhenUrlIsInvalid(): void
    {
        $this->rule->expression = IValidator::URL;
        Assert::throws(function () {
            $this->validator->validate('domain', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testStringMinimalLength(): void
    {
        $this->rule->expression = IValidator::MIN_LENGTH;
        $this->rule->argument = 10;
        Assert::true($this->validator->validate('asdasfdsb515sdvbsbf', $this->rule));
    }

    public function testThrowsExceptionWhenStingLengthIsTooShort(): void
    {
        $this->rule->expression = IValidator::MIN_LENGTH;
        $this->rule->argument = 10;
        Assert::throws(function () {
            $this->validator->validate('as', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testStringMaximalLength(): void
    {
        $this->rule->expression = IValidator::MAX_LENGTH;
        $this->rule->argument = 10;
        Assert::true($this->validator->validate('asdasd', $this->rule));
    }

    public function testIsNumberWithinRange(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(10, 20);
        Assert::true($this->validator->validate(15, $this->rule));
    }

    public function testIsNumberBiggerThenGiven(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(10, NULL);
        Assert::true($this->validator->validate(15, $this->rule));
    }

    public function testIsNumberLowerThenGiven(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(NULL, 10);
        Assert::true($this->validator->validate(5, $this->rule));
    }

    public function testIsRealNumber(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(NULL, NULL);
        Assert::true($this->validator->validate(5, $this->rule));
    }

    public function testRangeRuleThrowsExceptionIfValueIsNotOfNumericType(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(0, NULL);
        Assert::throws(function () {
            $this->validator->validate('adfa', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testRangeRuleThrowsExceptionIfNumberOfArgumentsIsInvalid(): void
    {
        $this->rule->expression = IValidator::RANGE;
        $this->rule->argument = array(NULL);
        Assert::throws(function () {
            $this->validator->validate('adfa', $this->rule);
        }, \Picabo\Restful\Exceptions\InvalidArgumentException::class);
    }

    public function testThrowsExceptionWhenStringIsTooLong(): void
    {
        $this->rule->expression = IValidator::MAX_LENGTH;
        $this->rule->argument = 10;
        Assert::throws(function () {
            $this->validator->validate('asad5aa18dvsa8dv49sd', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testStringLength(): void
    {
        $this->rule->expression = IValidator::LENGTH;
        $this->rule->argument = array(5, 10);
        Assert::true($this->validator->validate('ad6as46', $this->rule));
    }

    public function testThrowsExceptionWhenStringLegthIsOutOfRange(): void
    {
        $this->rule->expression = IValidator::LENGTH;
        $this->rule->argument = array(5, 10);
        Assert::throws(function () {
            $this->validator->validate('asad5aa18dvsa8dv49sd', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateIntegerValue(): void
    {
        $this->rule->expression = IValidator::INTEGER;
        Assert::true($this->validator->validate(456, $this->rule));
    }

    public function testThrowsExceptionWhenValueIsNotAnInteger(): void
    {
        $this->rule->expression = IValidator::INTEGER;
        Assert::throws(function () {
            $this->validator->validate('45', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateFloatValue(): void
    {
        $this->rule->expression = IValidator::FLOAT;
        Assert::true($this->validator->validate(45.45698, $this->rule));
    }

    public function testThrowsExceptionWhenValueIsNotFloat(): void
    {
        $this->rule->expression = IValidator::FLOAT;
        Assert::throws(function () {
            $this->validator->validate('45.56494', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateNumericValue(): void
    {
        $this->rule->expression = IValidator::NUMERIC;
        Assert::true($this->validator->validate('45.45698', $this->rule));
    }

    public function testThrowsExceptionWhenValueIsNotNumeric(): void
    {
        $this->rule->expression = IValidator::NUMERIC;
        Assert::throws(function () {
            $this->validator->validate('text', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testValidateUuid(): void
    {
        $this->rule->expression = IValidator::UUID;
        Assert::true($this->validator->validate('bfc5b0f9-a33a-4bf5-8745-0701114ce4f3', $this->rule));
    }

    public function testPassRequiredRuleValidationIfFieldIsNotNull(): void
    {
        $this->rule->expression = IValidator::REQUIRED;
        Assert::true($this->validator->validate('a', $this->rule));
    }

    public function testPassRequiredRuleValidationIfFieldIsZero(): void
    {
        $this->rule->expression = IValidator::REQUIRED;
        Assert::true($this->validator->validate(0, $this->rule));
    }

    public function testThrowsValidationExceptionIfRequiredFiledIsNull(): void
    {
        $this->rule->expression = IValidator::REQUIRED;
        Assert::throws(function () {
            $this->validator->validate(NULL, $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testThrowsExceptionWhenValueIsNotValidUUID(): void
    {
        $this->rule->expression = IValidator::UUID;
        Assert::throws(function () {
            $this->validator->validate('bfc5b0f9-a33a-4bf5-8745', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    public function testThrowsExceptionWhenCallbackToValidationFunctionIsNotCallable(): void
    {
        $this->validator->handle['test'] = 'Hello wordl!';
        $this->rule->expression = 'test';
        Assert::exception(function () {
            $this->validator->validate('test', $this->rule);
        }, \Picabo\Restful\Exceptions\InvalidStateException::class);
    }

    public function testPassCallbackRuleIfItReturnsTrue(): void
    {
        $this->rule->expression = IValidator::CALLBACK;
        $this->rule->argument = function ($value) {
            return true;
        };
        Assert::true($this->validator->validate('test', $this->rule));
    }

    public function testThrowsValidationExceptionIfCallbackValidatorResurnsFalse(): void
    {
        $this->rule->expression = IValidator::CALLBACK;
        $this->rule->argument = function ($value) {
            return false;
        };
        Assert::exception(function () {
            $this->validator->validate('test', $this->rule);
        }, \Picabo\Restful\Validation\Exceptions\ValidationException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->rule = new Rule;
        $this->validator = new Validator;
    }

}

(new ValidatorTest())->run();
