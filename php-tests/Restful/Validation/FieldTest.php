<?php

namespace Tests\Restful\Validation;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Validation\Exceptions\ValidationException;
use kalanis\Restful\Validation\Field;
use kalanis\Restful\Validation\IValidator;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class FieldTest extends TestCase
{

    private $validator;

    private Field $field;

    public function testAddRuleToField(): void
    {
        $this->field->addRule(IValidator::MAX_LENGTH, 'Please enter a value of at least %d characters.', [100]);
        $rules = $this->field->getRules();
        Assert::equal($rules[0]->getField(), 'test');
        Assert::equal($rules[0]->getMessage(), 'Please enter a value of at least %d characters.');
        Assert::equal($rules[0]->getArgument(), [100]);
        Assert::equal($rules[0]->getExpression(), IValidator::MAX_LENGTH);
    }

    public function testValidateFieldValue(): void
    {
        $this->field->addRule(IValidator::MAX_LENGTH, 'Please enter a value of at least %d characters.', [100]);
        $rules = $this->field->getRules();

        $this->validator->expects('validate')
            ->once()
            ->with('hello world', $rules[0])
            ->andReturn(NULL);

        $result = $this->field->validate('hello world');

        Assert::same([], $result);
    }

    public function testProvideErrorListWhenValidationFails(): void
    {
        $exception = new ValidationException('test', 'Please enter a value of at least 3 characters.');
        $this->field->addRule(IValidator::MAX_LENGTH, 'Please enter a value of at least %d characters.', [3]);
        $rules = $this->field->getRules();

        $this->validator->expects('validate')
            ->once()
            ->with('hello world', $rules[0])
            ->andThrow($exception);

        $result = $this->field->validate('hello world');

        Assert::same($result[0]->getField(), 'test');
        Assert::same($result[0]->getMessage(), 'Please enter a value of at least 3 characters.');
        Assert::equal($result[0]->getCode(), 0);
    }

    public function testSkipOptionalFieldIfIsNotSet(): void
    {
        $this->field->addRule(IValidator::EMAIL);

        $result = $this->field->validate(NULL);
        Assert::equal([], $result);
    }

    public function testSetValidationRuleCode(): void
    {
        $this->field->addRule(IValidator::EMAIL, 'Please enter valid email address', [NULL], 4025);
        $rule = $this->field->getRules()[0];
        Assert::equal($rule->code, 4025);
    }

    public function testFiledIsRequiredIfItHasRequiredRule(): void
    {
        $this->field->addRule(IValidator::MAX_LENGTH);
        $this->field->addRule(IValidator::REQUIRED);
        $required = $this->field->isRequired();
        Assert::true($required);
    }

    public function testFiledIsNotRequiredIfItHasNotRequiredRule(): void
    {
        $this->field->addRule(IValidator::MAX_LENGTH);
        $this->field->addRule(IValidator::MIN_LENGTH);
        $required = $this->field->isRequired();
        Assert::false($required);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Mockery::mock(\kalanis\Restful\Validation\Validator::class);
        $this->field = new Field('test', $this->validator);
    }
}


(new FieldTest())->run();
