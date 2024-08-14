<?php

namespace Drahak\Restful\Validation;

use Nette;

/**
 * Validation Rule caret
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
class Rule
{
    use Nette\SmartObject;

    public function __construct(
        protected string $field = '',
        protected string $message = '',
        protected int $code = 0,
        protected string $expression = '',
        protected array $argument = [],
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
    public function setCode(int $code): self
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
    public function setMessage(string $message): self
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
    public function setExpression(string $expression): self
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Get rule arguments
     * @return array
     */
    public function getArgument(): array
    {
        return $this->argument;
    }

    /**
     * Set rule argument(s)
     * @param array $argument
     */
    public function setArgument(array $argument): self
    {
        $this->argument = $argument;
        return $this;
    }
}
