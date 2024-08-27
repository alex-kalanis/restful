<?php

namespace Tests\Picabo\Restful\Validation;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Validation\IField;
use Picabo\Restful\Validation\IValidator;
use Picabo\Restful\Validation\Exceptions\ValidationException;
use Picabo\Restful\Validation\ValidationScope;
use Mockery;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Validation\ValidationScope.
 *
 * @testCase Tests\Picabo\Restful\Validation\ValidationScopeTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Validation
 */
class ValidationScopeTest extends TestCase
{

    private $validator;

    /** @var ValidationScope */
    private $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Mockery::mock(\Picabo\Restful\Validation\Validator::class);
        $this->schema = new ValidationScope($this->validator);
    }

    public function testCreateField(): void
    {
        $field = $this->schema->field('test');
        Assert::true($field instanceof IField);
        Assert::equal($field->getName(), 'test');
        Assert::equal($field->getValidator(), $this->validator);
    }

    public function testValidateArrayData(): void
    {
        $exception = new ValidationException('test', 'Please add integer');

        $testField = $this->schema->field('test');
        $testField->addRule(IValidator::INTEGER, 'Please add integer');
        $intigerRule = $testField->rules[0];

        $this->validator->expects('validate')
            ->once()
            ->with('Hello world', $intigerRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(array('test' => 'Hello world'));
        Assert::equal($errors[0]->field, 'test');
        Assert::equal($errors[0]->message, 'Please add integer');
    }

    public function testValidateDataUsingDotNotation(): void
    {
        $exception = new ValidationException('user.age', 'Please provide age as an integer');

        $ageField = $this->schema->field('user.age');
        $ageField->addRule(IValidator::INTEGER, 'Please provide age as an integer');
        $intigerRule = $ageField->rules[0];

        $this->validator->expects('validate')
            ->once()
            ->with('test', $intigerRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(array('user' => array('age' => 'test')));
        Assert::equal($errors[0]->field, 'user.age');
        Assert::equal($errors[0]->message, 'Please provide age as an integer');
    }

    public function testValidateMissingValueIfTheFieldIsRequired(): void
    {
        $exception = new ValidationException('user.name', 'Required field user.name is missing');

        $ageField = $this->schema->field('user.name');
        $ageField->addRule(IValidator::REQUIRED, "Please fill user name");
        $ageField->addRule(IValidator::MIN_LENGTH, "Min 10 chars", 10);
        $requiredRule = $ageField->rules[0];
        $minLengthRule = $ageField->rules[1];

        $this->validator->expects('validate')
            ->once()
            ->with('Ar', $requiredRule);

        $this->validator->expects('validate')
            ->once()
            ->with('Ar', $minLengthRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(array('user' => array('name' => 'Ar')));
        Assert::equal($errors[0]->field, 'user.name');
        Assert::equal($errors[0]->message, 'Required field user.name is missing');
    }

    public function testValidateInvalidValuesWhenUsingDotNotation(): void
    {
        $exception = new ValidationException('user.name', 'Required field user.name is missing');

        $ageField = $this->schema->field('user.name');
        $ageField->addRule(IValidator::REQUIRED, "Please fill user name");
        $requiredRule = $ageField->rules[0];

        $this->validator->expects('validate')
            ->once()
            ->with(NULL, $requiredRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(array('user' => 'tester'));
        Assert::equal($errors[0]->field, 'user.name');
        Assert::equal($errors[0]->message, 'Required field user.name is missing');
    }

    public function testValidateAllItemsInArray(): void
    {
        $exception = new ValidationException('user.name', 'Min 10 chars');

        $field = $this->schema->field('user.name');
        $field->addRule(IValidator::INTEGER, 'Min 10 chars');
        $rule = $field->rules[0];

        $this->validator->expects('validate')
            ->once()
            ->with('Test', $rule)
            ->andThrow($exception);
        $this->validator->expects('validate')
            ->once()
            ->with('Me', $rule)
            ->andThrow($exception);

        $errors = $this->schema->validate(array('user' => array(array('name' => 'Test'), array('name' => 'Me'))));
        Assert::equal($errors[0]->field, 'user.name');
        Assert::equal($errors[0]->message, 'Min 10 chars');
        Assert::equal($errors[1]->field, 'user.name');
        Assert::equal($errors[1]->message, 'Min 10 chars');
    }

}

(new ValidationScopeTest())->run();
