<?php

namespace kalanis\Restful\Validation;


/**
 * Validation Rule caret
 * @package kalanis\Restful\Validation
 */
class Rule
{

    /**
     * @param string $field
     * @param string $message
     * @param int $code
     * @param string $expression
     * @param array<bool|float|int|string|null> $argument
     */
    public function __construct(
        public string $field = '',
        public string $message = '',
        public int    $code = 0,
        public string $expression = '',
        public array  $argument = [],
    )
    {
    }

    /**
     * Get rule error code
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Set rule error code
     */
    public function setCode(int $code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get field name
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Set field name
     */
    public function setField(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get rule error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set rule error message
     */
    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get rule expression
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Set rule expression
     */
    public function setExpression(string $expression): static
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Get rule arguments
     * @return array<bool|float|int|string|null>
     */
    public function getArgument(): array
    {
        return $this->argument;
    }

    /**
     * Set rule argument(s)
     * @param array<bool|float|int|string|null> $argument
     */
    public function setArgument(array $argument): static
    {
        $this->argument = $argument;
        return $this;
    }
}
