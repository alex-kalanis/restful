<?php

namespace Drahak\Restful\Validation;

/**
 * Validation field interface
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 */
interface IField
{

    /**
     * Add rule to validation field
     * @param string $expression or identifier
     * @return IField
     */
    public function addRule($expression);

    /**
     * Validate field
     * @return mixed
     */
    public function validate(mixed $value);

    /**
     * Get field name
     * @return string
     */
    public function getName();

}