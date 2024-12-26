<?php

namespace kalanis\Restful\Validation;


use ArrayIterator;
use IteratorAggregate;
use Traversable;


/**
 * Validation error caret
 * @package kalanis\Restful\Validation
 * @--implements IteratorAggregate<int, array<string, string|int>>
 */
class Error implements IteratorAggregate
{

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

    /**
     * Converts error caret to an array
     * @return array<string, string|int>
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
