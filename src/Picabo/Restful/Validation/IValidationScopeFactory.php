<?php

namespace Picabo\Restful\Validation;

/**
 * IValidationScopeFactory
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
interface IValidationScopeFactory
{

    /**
     * Validation schema factory
     * @return IValidationScope
     */
    public function create(): IValidationScope;
}
