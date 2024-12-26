<?php

namespace kalanis\Restful\Validation;


/**
 * IValidationScope
 * @package kalanis\Restful\Validation
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
