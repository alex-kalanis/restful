<?php

namespace Drahak\Restful\Validation;

use Nette;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * ValidationScope
 * @package Drahak\Restful\Validation
 * @author Drahomír Hanák
 *
 * @property-read IValidator $validator
 */
class ValidationScope implements IValidationScope
{
    use Nette\SmartObject;

    /** @var Field[] */
    private $fields = [];

    public function __construct(private IValidator $validator)
    {
    }

    /****************** Validation scope interface ******************/

    /**
     * Create field or get existing
     * @param string $name
     * @return IField
     */
    public function field($name)
    {
        if (!isset($this->fields[$name])) {
            $this->fields[$name] = $this->createField($name);
        }
        return $this->fields[$name];
    }

    /**
     * Create field
     * @param string $name
     * @return Field
     */
    protected function createField($name)
    {
        return new Field($name, $this->getValidator());
    }

    /**
     * Get validator
     * @return mixed
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Validate all field in collection
     * @return Error[]
     */
    public function validate(array $data): array
    {
        $errors = [];
        /** @var IField $field */
        foreach ($this->fields as $field) {
            $fieldErrors = $this->validateDeeply($field, $data, $field->getName());
            $errors = array_merge($errors, $fieldErrors);
        }
        return $errors;
    }

    /****************** Getters & setters ******************/
    /**
     * Recursively validate data using dot notation
     * @param array $data
     * @param string $path
     */
    protected function validateDeeply(IField $field, $data, $path): array
    {
        $errors = [];

        if (Validators::isList($data) && count($data)) {
            foreach ($data as $item) {
                $newErrors = $this->validateDeeply($field, $item, $path);
                $errors = array_merge($errors, $newErrors);
            }
        } else {
            $keys = explode(".", $path);
            $last = count($keys) - 1;
            foreach ($keys as $index => $key) {
                $isLast = $index == $last;
                $value = $data[$key] ?? NULL;

                if (is_array($value)) {
                    $newPath = Strings::replace($path, "~^$key\.~");
                    $newErrors = $this->validateDeeply($field, $value, $newPath);
                    $errors = array_merge($errors, $newErrors);
                    break; // because recursion already handled this path validation
                } else if ($isLast || $value === NULL) {
                    $newErrors = $field->validate($value);
                    $errors = array_merge($errors, $newErrors);
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Get schema fields
     * @return IField[]
     */
    public function getFields()
    {
        return $this->fields;
    }

}
