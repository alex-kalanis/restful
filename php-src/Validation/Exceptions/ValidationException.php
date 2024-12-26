<?php

namespace kalanis\Restful\Validation\Exceptions;


use Exception;
use kalanis\Restful\Exceptions\LogicException;
use kalanis\Restful\Validation\Rule;
use Nette\Utils\Strings;


/**
 * ValidationException is thrown when validation problem appears
 * @package kalanis\Restful\Validation\Exceptions
 */
class ValidationException extends LogicException
{

    public function __construct(
        protected string $field,
        string           $message = "",
        int              $code = 0,
        Exception        $previous = null,
    )
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Validation exception simple factory
     */
    public static function createFromRule(
        Rule  $rule,
        mixed $value = null,
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
                ? "'" . Strings::truncate(strval($value), 60) . "' is invalid value: "
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
