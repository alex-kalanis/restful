<?php

namespace Picabo\Restful\Converters;

use Nette;
use Picabo\Restful\Utils\Strings;
use Traversable;

/**
 * CamelCaseConverter
 * @package Picabo\Restful\Converters
 * @author Drahomír Hanák
 */
class CamelCaseConverter implements IConverter
{
    use Nette\SmartObject;

    /**
     * Converts resource data keys to camelCase
     */
    public function convert(array $resource): array
    {
        $this->convertToCamel($resource);
        return $resource;
    }

    /**
     * Convert array keys to camel case
     * @param array|Traversable $array
     */
    private function convertToCamel(&$array): void
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array);
        }

        foreach (array_keys($array) as $key) {
            $value = &$array[$key];
            unset($array[$key]);

            $transformedKey = Strings::toCamelCase($key);
            if (is_iterable($value)) {
                $this->convertToCamel($value);
            }
            $array[$transformedKey] = $value;
            unset($value);
        }
    }
}
