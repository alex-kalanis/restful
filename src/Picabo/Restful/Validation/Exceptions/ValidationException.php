<?php

namespace Picabo\Restful\Validation\Exceptions;

use Exception;
use Nette\Utils\Strings;
use Picabo\Restful\Exceptions\LogicException;
use Picabo\Restful\Validation\Rule;

/**
 * ValidationException is thrown when validation problem appears
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class ValidationException extends LogicException
{

    public function __construct(
        protected string $field,
        string           $message = "",
        int              $code = 0,
        Exception        $previous = null
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Validation exception simple factory
     */
    public static function createFromRule(
        Rule  $rule,
        mixed $value = NULL
    ): self
    {
        $arg = $rule->getArgument();
        $printable = match (true) {
            is_object($arg) => [get_class($arg)],
            is_callable($arg) => [get_debug_type($arg)],
            default => (array) $arg,
        };

        return new self(
            $rule->getField(),
            (
            $value
                ? "'" . Strings::truncate($value, 60) . "' is invalid value: "
                : ''
            ) . vsprintf($rule->getMessage(), $printable),
            $rule->getCode(),
        );
    }

    /**
     * Get validation field name
     */
    public function getField(): string
    {
        return $this->field;
    }
}
