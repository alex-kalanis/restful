<?php

namespace kalanis\Restful\Validation;


/**
 * ValidationScopeFactory
 * @package kalanis\Restful\Validation
 */
class ValidationScopeFactory implements IValidationScopeFactory
{

    public function __construct(
        private readonly IValidator $validator,
    )
    {
    }

    /**
     * Validation schema factory
     * @return IValidationScope
     */
    public function create(): IValidationScope
    {
        return new ValidationScope($this->validator);
    }
}
