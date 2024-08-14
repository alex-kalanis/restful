<?php

namespace Drahak\Restful\Validation;

use Nette;

/**
 * Validation Rule caret
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 *
 * @property string $field name
 * @property string $message
 * @property int $code
 * @property string $expression
 * @property array $argument
 */
class Rule
{
    use Nette\SmartObject;

    /** @var string */
    protected $field;

    /** @var string */
    protected $message;

    /** @var int */
    protected $code;

    /** @var string */
    protected $expression;

    /** @var array */
    protected $argument;


    /******************** Getters & setters ********************/

    /**
     * Get rule error code
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set rule error code
     * @param int $code
     */
    public function setCode($code): static
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set field name
     * @param string $field
     */
    public function setField($field): static
    {
        $this->field = $field;
        return $this;
    }

    /**
     * Get rule error message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set rule error message
     * @param string $message
     */
    public function setMessage($message): static
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get rule expression
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * Set rule expression
     * @param string $expression
     */
    public function setExpression($expression): static
    {
        $this->expression = $expression;
        return $this;
    }

    /**
     * Get rule arguments
     * @return array
     */
    public function getArgument()
    {
        return $this->argument;
    }

    /**
     * Set rule argument(s)
     * @param array $argument
     */
    public function setArgument($argument): static
    {
        $this->argument = (array)$argument;
        return $this;
    }

}