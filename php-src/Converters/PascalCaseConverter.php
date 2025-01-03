<?php

namespace kalanis\Restful\Converters;


use kalanis\Restful\Utils\Strings;
use Traversable;


/**
 * PascalCaseConverter
 * @package kalanis\Restful\Converters
 * @template TK of string
 * @template TVal of mixed
 * @implements IConverter<TK, TVal>
 */
class PascalCaseConverter implements IConverter
{

    /**
     * Converts resource data keys to PascalCase
     * @param array<TK, TVal> $resource
     * @return array<TK, TVal>
     */
    public function convert(array $resource): array
    {
        $this->convertToPascal($resource);
        return $resource;
    }

    /**
     * Convert array keys to camel case
     * @param array<TK, TVal>|Traversable<TK, TVal> $array
     */
    private function convertToPascal(iterable &$array): void
    {
        if ($array instanceof Traversable) {
            $array = iterator_to_array($array);
        }

        foreach (array_keys($array) as $key) {
            $value = &$array[$key];
            unset($array[$key]);

            $transformedKey = Strings::toPascalCase($key);
            if (is_iterable($value)) {
                $this->convertToPascal($value);
            }
            $array[$transformedKey] = $value;
            unset($value);
        }
    }
}
