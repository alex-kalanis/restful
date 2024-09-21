<?php

namespace Picabo\Restful\Validation;

/**
 * IValidationScope
 * @package Picabo\Restful\Validation
 * @author Drahomír Hanák
 */
interface IValidationScope
{

    /**
     * Create field or get existing
     * @param string $name
     * @return IField
     */
    public function field(string $name): IField;

    /**
     * Validate all field in collection
     * @param array<string|int, mixed> $data
     * @return Error[]
     */
    public function validate(array $data): array;

}
