<?php

namespace Drahak\Restful\Converters;

use Drahak\Restful\Utils\Strings;
use Nette;
use Traversable;

/**
 * SnakeCaseConverter
 * @package Drahak\Restful\Converters
 * @author Drahomír Hanák
 */
class SnakeCaseConverter implements IConverter
{
    use Nette\SmartObject;

    /**
     * Converts resource data keys to snake_case
     */
    public function convert(array $resource): array
    {
        $this->convertToSnake($resource);
        return $resource;
    }

    /**
     * Convert array keys to snake case
     * @param array|Traversable $array
     */
    private function convertToSnake(&$array)
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array);
        }

        foreach (array_keys($array) as $key) {
            $value = &$array[$key];
            unset($array[$key]);

            $transformedKey = Strings::toSnakeCase($key);
            if (is_iterable($value)) {
                $this->convertToSnake($value);
            }
            $array[$transformedKey] = $value;
            unset($value);
        }
    }

}
