<?php

namespace Picabo\Restful\Validation;

use ArrayIterator;
use IteratorAggregate;
use Nette;
use Traversable;

/**
 * Validation error caret
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class Error implements IteratorAggregate
{
    use Nette\SmartObject;

    public function __construct(
        private readonly string $field,
        private readonly string $message,
        private readonly int    $code,
    )
    {
    }

    /**
     * Get error code
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /****************** Getters ******************/

    /**
     * Get error field name
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get validation error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Iterate through error data to convert it
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }

    /****************** Iterator aggregate interface ******************/

    /**
     * Converts error caret to an array
     */
    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}
