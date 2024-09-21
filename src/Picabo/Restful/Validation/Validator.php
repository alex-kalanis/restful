<?php

namespace Picabo\Restful\Validation;

use Nette;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Picabo\Restful\Exceptions\InvalidArgumentException;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Validation\Exceptions\ValidationException;

/**
 * Rule validator
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class Validator implements IValidator
{
    use Nette\SmartObject;

    /** @var array<string, array<string, string>|callable|callable-string> Command handle callbacks */
    public array $handle = [
        self::EMAIL => [self::class, 'validateEmail'],
        self::URL => [self::class, 'validateUrl'],
        self::REGEXP => [self::class, 'validateRegexp'],
        self::EQUAL => [self::class, 'validateEquality'],
        self::UUID => [self::class, 'validateUuid'],
        self::CALLBACK => [self::class, 'validateCallback'],
        self::REQUIRED => [self::class, 'validateRequired'],
    ];

    /**
     * Validate callback rule
     * @param string|numeric|null $value
     *
     * @throws  ValidationException If callback returns false
     */
    public static function validateCallback(mixed $value, Rule $rule): void
    {
        $arguments = $rule->getArgument();
        if (isset($arguments[0]) && is_callable($arguments[0])) {
            $result = $arguments[0]($value);
            if (true === $result) {
                return;
            }
        }
        throw ValidationException::createFromRule($rule, $value);
    }

    /**
     * Validate required rule
     * @param string|numeric|null $value
     *
     * @throws  ValidationException If field value is missing (is NULL)
     */
    public static function validateRequired(mixed $value, Rule $rule): void
    {
        if ($value === NULL) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /******************** Special validators ********************/
    /**
     * Validate regexp
     *
     * @throws InvalidArgumentException
     * @throws ValidationException
     */
    public static function validateRegexp(mixed $value, Rule $rule): void
    {
        if (!isset($rule->getArgument()[0])) {
            throw new InvalidArgumentException('No regular expression found in pattern validation rule');
        }

        if (!Strings::match(strval($value), strval($rule->getArgument()[0]))) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Validate equality
     * @param string $value
     * @throws ValidationException
     */
    public static function validateEquality(mixed $value, Rule $rule): void
    {
        if (!in_array(strval($value), $rule->getArgument())) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Validate email
     * @param string $value
     * @throws ValidationException
     */
    public static function validateEmail(mixed $value, Rule $rule): void
    {
        if (!Validators::isEmail(strval($value))) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Validate URL
     * @param string $value
     * @throws ValidationException
     */
    public static function validateUrl(mixed $value, Rule $rule): void
    {
        if (!Validators::isUrl(strval($value))) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Validate UUID
     * @param string $value
     * @throws ValidationException
     */
    public static function validateUuid(mixed $value, Rule $rule): void
    {
        $isUuid = (bool)preg_match("/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i", strval($value));
        if (!$isUuid) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Validate value for this rule
     * @param mixed $value
     * @param Rule $rule
     * @return void
     * @throws InvalidStateException
     * @throws ValidationException
     */
    public function validate(mixed $value, Rule $rule): void
    {
        if (isset($this->handle[$rule->getExpression()])) {
            $callback = $this->handle[$rule->getExpression()];
            if (!is_callable($callback)) {
                throw new InvalidStateException(
                    'Handle for expression ' . $rule->getExpression() . ' not found or is not callable');
            }
            $params = [$value, $rule];
            call_user_func_array($callback, $params);
            return;
        }

        $expression = $this->parseExpression($rule);
        if (!Validators::is($value, $expression)) {
            throw ValidationException::createFromRule($rule, $value);
        }
    }

    /**
     * Parse nette validator expression
     */
    private function parseExpression(Rule $rule): string
    {
        $givenArgumentsCount = count($rule->getArgument());
        $expectedArgumentsCount = substr_count($rule->getExpression(), '%');
        if ($expectedArgumentsCount != $givenArgumentsCount) {
            throw new InvalidArgumentException(
                'Invalid number of arguments for expression "' . $rule->getExpression() . '". Expected ' . $expectedArgumentsCount . ', ' . $givenArgumentsCount . ' given.'
            );
        }
        return vsprintf($rule->getExpression(), $rule->getArgument());
    }

}
