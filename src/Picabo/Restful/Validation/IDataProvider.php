<?php

namespace Picabo\Restful\Validation;

/**
 * Validation data provider
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
interface IDataProvider
{

    /**
     * Get validation field
     * @param string $name
     * @return IField
     */
    public function field(string $name): IField;

    /**
     * Validate data
     * @return array<Error>
     */
    public function validate(): array;

    /**
     * Is input valid
     * @return bool
     */
    public function isValid(): bool;

    /**
     * Get validation schema
     * @return IValidationScope
     */
    public function getValidationScope(): IValidationScope;

}