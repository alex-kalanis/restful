<?php

namespace Picabo\Restful\Validation;

use Nette;

/**
 * ValidationScopeFactory
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
class ValidationScopeFactory implements IValidationScopeFactory
{
    use Nette\SmartObject;

    public function __construct(
        private readonly IValidator $validator
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
