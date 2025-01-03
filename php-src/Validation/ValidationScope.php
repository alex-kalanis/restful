<?php

namespace kalanis\Restful\Validation;


use Nette\Utils\Arrays;
use Nette\Utils\Strings;


/**
 * ValidationScope
 * @package kalanis\Restful\Validation
 */
class ValidationScope implements IValidationScope
{

    /** @var Field[] */
    private array $fields = [];

    public function __construct(
        private readonly IValidator $validator,
    )
    {
    }

    /**
     * Create field or get existing
     * @param string $name
     * @return IField
     */
    public function field(string $name): IField
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
    protected function createField(string $name): IField
    {
        return new Field($name, $this->getValidator());
    }

    /**
     * Get validator
     */
    public function getValidator(): IValidator
    {
        return $this->validator;
    }

    /**
     * Validate all field in collection
     * @param array<string|int, mixed> $data
     * @return Error[]
     */
    public function validate(array $data): array
    {
        $errors = [];
        foreach ($this->fields as $field) {
            $fieldErrors = $this->validateDeeply($field, $data, $field->getName());
            $errors = array_merge($errors, $fieldErrors);
        }
        return $errors;
    }

    /**
     * Recursively validate data using dot notation
     * @param IField $field
     * @param array<string|int, mixed> $data
     * @param string $path
     * @return array<Error>
     */
    protected function validateDeeply(IField $field, array $data, string $path): array
    {
        $errors = [];

        if (Arrays::isList($data) && count($data)) {
            foreach ($data as $item) {
                $newErrors = $this->validateDeeply($field, $item, $path);
                $errors = array_merge($errors, $newErrors);
            }
        } else {
            $keys = explode(".", $path);
            $last = count($keys) - 1;
            foreach ($keys as $index => $key) {
                $isLast = $index == $last;
                $value = $data[$key] ?? null;

                if (is_array($value)) {
                    $newPath = Strings::replace($path, "~^$key\.~");
                    $newErrors = $this->validateDeeply($field, $value, $newPath);
                    $errors = array_merge($errors, $newErrors);
                    break; // because recursion already handled this path validation
                } elseif ($isLast || is_null($value)) {
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
    public function getFields(): array
    {
        return $this->fields;
    }
}
