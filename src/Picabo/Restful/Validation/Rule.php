<?php

namespace Picabo\Restful\Validation;

use Nette;

/**
 * Validation Rule caret
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class Rule
{
    use Nette\SmartObject;

    public function __construct(
        public string $field = '',
        public string $message = '',
        public int    $code = 0,
        public string $expression = '',
        public array|string|\Closure  $argument = [],
    )
    {
    }

    /******************** Getters & setters ********************/

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
     * @return array|string|\Closure
     */
    public function getArgument(): array|string|\Closure
    {
        return $this->argument;
    }

    /**
     * Set rule argument(s)
     * @param array|string|\Closure $argument
     */
    public function setArgument(array|string|\Closure $argument): static
    {
        $this->argument = $argument;
        return $this;
    }
}
