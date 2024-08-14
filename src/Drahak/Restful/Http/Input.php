<?php

namespace Drahak\Restful\Http;

use ArrayIterator;
use Drahak\Restful\Validation\IDataProvider;
use Drahak\Restful\Validation\IField;
use Drahak\Restful\Validation\IValidationScope;
use Drahak\Restful\Validation\IValidationScopeFactory;
use Exception;
use IteratorAggregate;
use Nette;
use Nette\MemberAccessException;

/**
 * Request Input parser
 * @package Drahak\Restful\Http
 * @author Drahomír Hanák
 *
 * @property array $data
 */
class Input implements IteratorAggregate, IInput, IDataProvider
{
    use Nette\SmartObject;

    /** @var IValidationScope */
    private $validationScope;

    public function __construct(private IValidationScopeFactory $validationScopeFactory, private array $data = [])
    {
    }

    /******************** IInput ********************/

    /**
     * @param string $name
     * @return mixed
     *
     * @throws Exception|MemberAccessException
     */
    public function &__get($name)
    {
        $data = $this->getData();
        if (array_key_exists($name, $data)) {
            return $data[$name];
        }
        throw new MemberAccessException('Cannot read an undeclared property ' . static::class . '::$' . $name . '.');
    }

    /**
     * Get parsed input data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /******************** Magic methods ********************/
    /**
     * Set input data
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
    public function __isset($name)
    {
        $data = $this->getData();
        return array_key_exists($name, $data);
    }

    /******************** Iterator aggregate interface ********************/

    /**
     * Get input data iterator
     * @return InputIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getData());
    }

    /******************** Validation data provider interface ********************/

    /**
     * Get validation field
     * @param string $name
     * @return IField
     */
    public function field($name)
    {
        return $this->getValidationScope()->field($name);
    }

    /**
     * Get validation scope
     * @return IValidationScope
     */
    public function getValidationScope()
    {
        if (!$this->validationScope) {
            $this->validationScope = $this->validationScopeFactory->create();
        }
        return $this->validationScope;
    }

    /**
     * Is input valid
     * @return bool
     */
    public function isValid()
    {
        return !$this->validate();
    }

    /**
     * Validate input data
     * @return array
     */
    public function validate()
    {
        return $this->getValidationScope()->validate($this->getData());
    }

}
