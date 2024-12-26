<?php

namespace Tests\Restful\Http;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Http\Input;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class InputTest extends TestCase
{

    private array $data;

    private $validationScope;

    private $validationScopeFactory;

    private Input $input;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = ['hello_message' => 'Hello World'];
        $this->validationScope = Mockery::mock(\kalanis\Restful\Validation\ValidationScope::class);
        $this->validationScopeFactory = Mockery::mock(\kalanis\Restful\Validation\IValidationScopeFactory::class);
        $this->input = new Input($this->validationScopeFactory, $this->data);
    }

    public function testGetData(): void
    {
        $data = $this->input->getData();
        Assert::same($data, $this->data);
    }

    public function testGetData_callingPropertyName_shouldReturnNameValueFromData(): void
    {
        $data = $this->input->setData(['name' => 'John Doe']);
        Assert::equal('John Doe', $this->input->name);
    }

    public function testGetData_callingPropertyData_shouldReturnDataValueFromData(): void
    {
        $data = $this->input->setData(['data' => 'test data', 'private' => 'private value', 'object' => ['id' => 5, 'name' => 'row']]);
        Assert::equal('test data', $this->input->data);
    }

    public function testGetData_callingPropertyPrivate_shouldReturnPropertyValueFromData(): void
    {
        $data = $this->input->setData(['data' => 'test data', 'private' => 'private value', 'object' => ['id' => 5, 'name' => 'row']]);
        Assert::equal('private value', $this->input->private);
    }

    public function testGetData_callingPropertyObject_shouldReturnObjectValueFromData(): void
    {
        $data = $this->input->setData(['data' => 'test data', 'private' => 'private value', 'object' => ['id' => 5, 'name' => 'row']]);
        Assert::equal(['id' => 5, 'name' => 'row'], $this->input->object);
    }

    public function testGetData_callingInvalidProperty_shouldThrowException(): void
    {
        $data = $this->input->setData(['data' => 'test data', 'private' => 'private value', 'object' => ['id' => 5, 'name' => 'row']]);
        Assert::exception(function () {
            $this->input->unknown;
        }, \Nette\MemberAccessException::class, 'Cannot read an undeclared property kalanis\Restful\Http\Input::$unknown.');
    }

    public function testGetValidationField(): void
    {
        $field = Mockery::mock(\kalanis\Restful\Validation\Field::class);
        $this->validationScopeFactory->expects('create')
            ->once()
            ->andReturn($this->validationScope);

        $this->validationScope->expects('field')
            ->once()
            ->with('name')
            ->andReturn($field);

        $result = $this->input->field('name');
        Assert::same($result, $field);
    }

    public function testValidateInputData(): void
    {
        $errors = [];

        $this->validationScopeFactory->expects('create')
            ->once()
            ->andReturn($this->validationScope);
        $this->validationScope->expects('validate')
            ->once()
            ->with($this->data)
            ->andReturn($errors);

        $result = $this->input->validate();
        Assert::equal($result, $errors);
    }

    public function testIfInputDataIsValid(): void
    {
        $errors = [];

        $this->validationScopeFactory->expects('create')
            ->once()
            ->andReturn($this->validationScope);
        $this->validationScope->expects('validate')
            ->once()
            ->with($this->data)
            ->andReturn($errors);

        $result = $this->input->isValid();
        Assert::true($result);
    }
}


(new InputTest())->run();
