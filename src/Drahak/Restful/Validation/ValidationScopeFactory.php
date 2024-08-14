<?php

namespace Drahak\Restful\Validation;

use Nette;

/**
 * ValidationScopeFactory
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
class ValidationScopeFactory implements IValidationScopeFactory
{
    use Nette\SmartObject;

    public function __construct(private IValidator $validator)
    {
    }

    /**
     * Validation schema factory
     * @return IValidationScope
     */
    public function create()
    {
        return new ValidationScope($this->validator);
    }


}
