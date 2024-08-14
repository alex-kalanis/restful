<?php

namespace Drahak\Restful\Validation;

use Drahak\Restful\LogicException;
use Exception;
use Nette\Utils\Strings;

/**
 * ValidationException is thrown when validation problem appears
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
class ValidationException extends LogicException
{

    /**
     * @param string $field
     * @param string $message
     * @param int $code
     */
    public function __construct(protected $field, $message = "", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Validation exception simple factory
     */
    public static function createFromRule(Rule $rule, mixed $value = NULL): self
    {
        return new self($rule->getField(), ($value ? "'" . Strings::truncate($value, 60) . "' is invalid value: " : '') . vsprintf($rule->getMessage(), $rule->getArgument()), $rule->getCode());
    }

    /**
     * Get validation field name
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

}
