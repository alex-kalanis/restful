<?php

namespace Tests\Picabo\Restful\Http;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Http\Input;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Http\Input.
 *
 * @testCase Tests\Picabo\Restful\Http\InputTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Http
 */
class InputTest extends TestCase
{

    /** @var array */
    private $data;

    private $validationScope;

    private $validationScopeFactory;

    /** @var Input */
    private $input;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = array('hello_message' => 'Hello World');
        $this->validationScope = Mockery::mock(\Picabo\Restful\Validation\ValidationScope::class);
        $this->validationScopeFactory = Mockery::mock(\Picabo\Restful\Validation\IValidationScopeFactory::class);
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
        }, \Nette\MemberAccessException::class, 'Cannot read an undeclared property Picabo\Restful\Http\Input::$unknown.');
    }

    public function testGetValidationField(): void
    {
        $field = Mockery::mock(\Picabo\Restful\Validation\Field::class);
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
        $errors = array();

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
        $errors = array();

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
