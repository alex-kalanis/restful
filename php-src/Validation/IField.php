<?php

namespace kalanis\Restful\Validation;


/**
 * Validation field interface
 * @package kalanis\Restful\Validation
 */
interface IField
{

    /**
     * Add rule to validation field
     * @param string $expression or identifier
     * @return IField
     */
    public function addRule(string $expression): IField;

    /**
     * Validate field
     * @param mixed $value
     * @return Error[]
     */
    public function validate(mixed $value): array;

    /**
     * Get field name
     * @return string
     */
    public function getName(): string;
}
