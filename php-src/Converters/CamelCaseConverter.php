<?php

namespace kalanis\Restful\Converters;


use kalanis\Restful\Utils\Strings;
use Traversable;


/**
 * CamelCaseConverter
 * @package kalanis\Restful\Converters
 * @template TK of string
 * @template TVal of mixed
 * @implements IConverter<TK, TVal>
 */
class CamelCaseConverter implements IConverter
{

    /**
     * Converts resource data keys to camelCase
     * @param array<TK, TVal> $resource
     * @return array<TK, TVal>
     */
    public function convert(array $resource): array
    {
        $this->convertToCamel($resource);
        return $resource;
    }

    /**
     * Convert array keys to camel case
     * @param array<TK, TVal>|Traversable<TK, TVal> $array
     */
    private function convertToCamel(iterable &$array): void
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
