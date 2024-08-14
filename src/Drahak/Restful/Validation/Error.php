<?php

namespace Drahak\Restful\Validation;

use ArrayIterator;
use IteratorAggregate;
use Nette;
use Traversable;

/**
 * Validation error caret
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 *
 * @property-read string $field
 * @property-read string $message
 * @property-read int $code
 */
class Error implements IteratorAggregate
{
    use Nette\SmartObject;

    /**
     * @param string $field
     * @param string $message
     * @param int $code
     */
    public function __construct(private $field, private $message, private $code)
    {
    }

    /**
     * Get error code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /****************** Getters ******************/

    /**
     * Get error field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Get validation error message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Iterate through error data to convert it
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->toArray());
    }

    /****************** Iterator aggregate interface ******************/

    /**
     * Converts error caret to an array
     * @return array
     */
    public function toArray()
    {
        return ['field' => $this->field, 'message' => $this->message, 'code' => $this->code];
    }

}
