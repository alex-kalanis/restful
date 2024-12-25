<?php

namespace Picabo\Restful\Validation;

use Picabo\Restful\Validation\Exceptions\ValidationException;

/**
 * Validator interface
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
interface IValidator
{

    // Equality rules
    public const EQUAL = ':equal';
    public const IS_IN = ':equal';
    public const REQUIRED = 'required';

    // Textual rules
    public const MIN_LENGTH = 'string:%d..';
    public const MAX_LENGTH = 'string:..%d';
    public const LENGTH = 'string:%d..%d';
    public const EMAIL = ':email';
    public const URL = ':url';
    public const REGEXP = ':regexp';
    public const PATTERN = ':regexp'; // same as regexp

    // Numeric rules
    public const INTEGER = 'int';
    public const NUMERIC = 'numeric';
    public const FLOAT = 'float';
    public const RANGE = 'numeric:%s..%s';

    // Special
    public const UUID = 'uuid';
    public const CALLBACK = 'callback';

    /**
     * Validate value with rule
     * @param mixed $value
     * @param Rule $rule
     * @throws ValidationException
     * @return void
     */
    public function validate(mixed $value, Rule $rule): void;

}
