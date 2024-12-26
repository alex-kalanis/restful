<?php

namespace Tests\Restful\Validation;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Validation\Exceptions\ValidationException;
use kalanis\Restful\Validation\IField;
use kalanis\Restful\Validation\IValidator;
use kalanis\Restful\Validation\ValidationScope;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class ValidationScopeTest extends TestCase
{

    private $validator;

    private ValidationScope $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validator = Mockery::mock(\kalanis\Restful\Validation\Validator::class);
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
        $intigerRule = $testField->getRules()[0];

        $this->validator->expects('validate')
            ->once()
            ->with('Hello world', $intigerRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(['test' => 'Hello world']);
        Assert::equal($errors[0]->getField(), 'test');
        Assert::equal($errors[0]->getMessage(), 'Please add integer');
    }

    public function testValidateDataUsingDotNotation(): void
    {
        $exception = new ValidationException('user.age', 'Please provide age as an integer');

        $ageField = $this->schema->field('user.age');
        $ageField->addRule(IValidator::INTEGER, 'Please provide age as an integer');
        $intigerRule = $ageField->getRules()[0];

        $this->validator->expects('validate')
            ->once()
            ->with('test', $intigerRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(['user' => ['age' => 'test']]);
        Assert::equal($errors[0]->getField(), 'user.age');
        Assert::equal($errors[0]->getMessage(), 'Please provide age as an integer');
    }

    public function testValidateMissingValueIfTheFieldIsRequired(): void
    {
        $exception = new ValidationException('user.name', 'Required field user.name is missing');

        $ageField = $this->schema->field('user.name');
        $ageField->addRule(IValidator::REQUIRED, "Please fill user name");
        $ageField->addRule(IValidator::MIN_LENGTH, "Min 10 chars", [10]);
        $requiredRule = $ageField->getRules()[0];
        $minLengthRule = $ageField->getRules()[1];

        $this->validator->expects('validate')
            ->once()
            ->with('Ar', $requiredRule);

        $this->validator->expects('validate')
            ->once()
            ->with('Ar', $minLengthRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(['user' => ['name' => 'Ar']]);
        Assert::equal($errors[0]->getField(), 'user.name');
        Assert::equal($errors[0]->getMessage(), 'Required field user.name is missing');
    }

    public function testValidateInvalidValuesWhenUsingDotNotation(): void
    {
        $exception = new ValidationException('user.name', 'Required field user.name is missing');

        $ageField = $this->schema->field('user.name');
        $ageField->addRule(IValidator::REQUIRED, "Please fill user name");
        $requiredRule = $ageField->getRules()[0];

        $this->validator->expects('validate')
            ->once()
            ->with(NULL, $requiredRule)
            ->andThrow($exception);

        $errors = $this->schema->validate(['user' => 'tester']);
        Assert::equal($errors[0]->getField(), 'user.name');
        Assert::equal($errors[0]->getMessage(), 'Required field user.name is missing');
    }

    public function testValidateAllItemsInArray(): void
    {
        $exception = new ValidationException('user.name', 'Min 10 chars');

        $field = $this->schema->field('user.name');
        $field->addRule(IValidator::INTEGER, 'Min 10 chars');
        $rule = $field->getRules()[0];

        $this->validator->expects('validate')
            ->once()
            ->with('Test', $rule)
            ->andThrow($exception);
        $this->validator->expects('validate')
            ->once()
            ->with('Me', $rule)
            ->andThrow($exception);

        $errors = $this->schema->validate(['user' => [['name' => 'Test'], ['name' => 'Me']]]);
        Assert::equal($errors[0]->getField(), 'user.name');
        Assert::equal($errors[0]->getMessage(), 'Min 10 chars');
        Assert::equal($errors[1]->getField(), 'user.name');
        Assert::equal($errors[1]->getMessage(), 'Min 10 chars');
    }
}


(new ValidationScopeTest())->run();
