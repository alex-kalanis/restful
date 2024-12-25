<?php

namespace Picabo\Restful\Http;

use ArrayIterator;
use Exception;
use IteratorAggregate;
use Nette;
use Nette\MemberAccessException;
use Picabo\Restful\Validation\Error;
use Picabo\Restful\Validation\IDataProvider;
use Picabo\Restful\Validation\IField;
use Picabo\Restful\Validation\IValidationScope;
use Picabo\Restful\Validation\IValidationScopeFactory;

/**
 * Request Input parser
 * @package Picabo\Restful\Http
 * @author Drahomír Hanák
 * @template TK of string
 * @template TVal of mixed
 * @implements IteratorAggregate<TK, TVal>
 */
class Input implements IteratorAggregate, IInput, IDataProvider
{
    use Nette\SmartObject;

    private ?IValidationScope $validationScope = null;

    /**
     * @param IValidationScopeFactory $validationScopeFactory
     * @param array<TK, TVal> $data
     */
    public function __construct(
        private readonly IValidationScopeFactory $validationScopeFactory,
        private array                            $data = [],
    )
    {
    }

    /******************** IInput ********************/

    /**
     * @param string $name
     * @throws Exception|MemberAccessException
     * @return mixed
     *
     */
    public function &__get(string $name)
    {
        $data = $this->getData();
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        throw new MemberAccessException('Cannot read an undeclared property ' . static::class . '::$' . $name . '.');
    }

    /**
     * Get parsed input data
     * @return array<TK, TVal>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /******************** Magic methods ********************/
    /**
     * Set input data
     * @param array<TK, TVal> $data
     * @return $this
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset(string $name)
    {
        $data = $this->getData();
        return array_key_exists($name, $data);
    }

    /******************** Iterator aggregate interface ********************/

    /**
     * Get input data iterator
     * @return ArrayIterator<TK, TVal>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->getData());
    }

    /******************** Validation data provider interface ********************/

    /**
     * Get validation field
     * @param string $name
     * @return IField
     */
    public function field(string $name): IField
    {
        return $this->getValidationScope()->field($name);
    }

    /**
     * Get validation scope
     * @return IValidationScope
     */
    public function getValidationScope(): IValidationScope
    {
        if (empty($this->validationScope)) {
            $this->validationScope = $this->validationScopeFactory->create();
        }
        return $this->validationScope;
    }

    /**
     * Is input valid
     * @return bool
     */
    public function isValid(): bool
    {
        return !$this->validate();
    }

    /**
     * Validate input data
     * @return array<Error>
     */
    public function validate(): array
    {
        return $this->getValidationScope()->validate($this->getData());
    }

}
