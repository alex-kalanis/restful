<?php

namespace Picabo\Restful\Converters;

use Nette;
use Picabo\Restful\Utils\Strings;
use Traversable;

/**
 * SnakeCaseConverter
 * @package Picabo\Restful\Converters
 * @author Drahomír Hanák
 * @template TK of string
 * @template TVal of mixed
 * @implements IConverter<TK, TVal>
 */
class SnakeCaseConverter implements IConverter
{
    use Nette\SmartObject;

    /**
     * Converts resource data keys to snake_case
     * @param array<TK, TVal> $resource
     * @return array<TK, TVal>
     */
    public function convert(array $resource): array
    {
        $this->convertToSnake($resource);
        return $resource;
    }

    /**
     * Convert array keys to snake case
     * @param array<TK, TVal>|Traversable<TK, TVal> $array
     */
    private function convertToSnake(iterable &$array): void
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
