<?php

namespace kalanis\Restful\Validation;


/**
 * IValidationScopeFactory
 * @package kalanis\Restful\Validation
 */
interface IValidationScopeFactory
{

    /**
     * Validation schema factory
     * @return IValidationScope
     */
    public function create(): IValidationScope;
}
