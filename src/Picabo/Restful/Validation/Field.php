<?php

namespace Picabo\Restful\Validation;

use Nette;
use Nette\Utils\Validators;
use Picabo\Restful\Validation\Exceptions\ValidationException;

/**
 * Validation field
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class Field implements IField
{
    use Nette\SmartObject;

    /** @var array<string, string> Default field error messages for validator */
    public static array $defaultMessages = [
        IValidator::EQUAL => 'Please enter %s.',
        IValidator::MIN_LENGTH => 'Please enter a value of at least %d characters.',
        IValidator::MAX_LENGTH => 'Please enter a value no longer than %d characters.',
        IValidator::LENGTH => 'Please enter a value between %d and %d characters long.',
        IValidator::EMAIL => 'Please enter a valid email address.',
        IValidator::URL => 'Please enter a valid URL.',
        IValidator::INTEGER => 'Please enter a numeric value.',
        IValidator::FLOAT => 'Please enter a numeric value.',
        IValidator::RANGE => 'Please enter a value between %d and %d.',
        IValidator::UUID => 'Please enter a valid UUID.'
    ];

    /** @var string[] Numeric expressions that needs to convert value from string (because of x-www-form-urlencoded) */
    protected static array $numericExpressions = [
        IValidator::INTEGER,
        IValidator::FLOAT,
        IValidator::NUMERIC,
        IValidator::RANGE
    ];

    /** @var Rule[] */
    protected array $rules = [];

    public function __construct(
        protected readonly string     $name,
        protected readonly IValidator $validator
    )
    {
    }

    /**
     * Add validation rule for this field
     * @param string $expression
     * @param string|null $message
     * @param array<bool|float|int|string|null> $argument
     * @param int $code
     * @return IField
     */
    public function addRule(string $expression, ?string $message = null, array $argument = [], int $code = 0): IField
    {
        $rule = new Rule(
            $this->name,
            strval($message),
            $code,
            $expression,
            $argument,
        );

        if (NULL === $message && isset(self::$defaultMessages[$expression])) {
            $rule->setMessage(self::$defaultMessages[$expression]);
        }

        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Validate field for given value
     * @param mixed $value
     * @return Error[]
     */
    public function validate(mixed $value): array
    {
        if (!$this->isRequired() && NULL === $value) {
            return [];
        }

        $errors = [];
        foreach ($this->rules as $rule) {
            try {
                if (in_array($rule->getExpression(), static::$numericExpressions)) {
                    $value = $this->parseNumericValue($value);
                }

                $this->validator->validate($value, $rule);
            } catch (ValidationException $e) {
                $errors[] = new Error($e->getField(), $e->getMessage(), $e->getCode());
            }
        }
        return $errors;
    }

    /**
     * Is field required
     */
    public function isRequired(): bool
    {
        foreach ($this->rules as $rule) {
            if (IValidator::REQUIRED === $rule->getExpression()) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Convert string -> int, string -> float because of textual x-www-form-data
     * @param mixed $value
     * @return mixed
     */
    protected function parseNumericValue(mixed $value): mixed
    {
        if (Validators::isNumericInt($value)) {
            return intval($value);
        }
        if (Validators::isNumeric($value)) {
            return floatval($value);
        }
        return $value;
    }

    /**
     * Get field case-sensitive name
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get field rules
     * @return Rule[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get validator
     * @return IValidator
     */
    public function getValidator(): IValidator
    {
        return $this->validator;
    }
}
