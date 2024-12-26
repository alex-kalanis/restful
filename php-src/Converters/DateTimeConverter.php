<?php

namespace kalanis\Restful\Converters;


use DateTime;
use DateTimeInterface;
use Traversable;


/**
 * DateTimeConverter
 * @package kalanis\Restful\Converters
 * @template TK of string
 * @template TVal of mixed
 * @implements IConverter<TK, TVal>
 */
class DateTimeConverter implements IConverter
{

    /**
     * @param string $format of date time
     */
    public function __construct(
        private readonly string $format = 'c',
    )
    {
    }

    /**
     * Converts DateTime objects in resource to string
     * @param array<TK, TVal> $resource
     * @return array<int|string, mixed>
     */
    public function convert(array $resource): array
    {
        return (array) $this->parseDateTimeToString($resource);
    }

    /**
     * @param array<TK, TVal>|Traversable<TK, TVal>|string|int|object $array
     * @return array<string, mixed>|string
     */
    private function parseDateTimeToString(mixed $array): array|string
    {
        if (!is_array($array)) {
            if ($array instanceof DateTime || interface_exists('DateTimeInterface') && $array instanceof DateTimeInterface) {
                return $array->format($this->format);
            }
            return strval($array);
        }

        foreach ($array as $key => $value) {
            if (is_iterable($array)) {
                /** @var string|int|object $value */
                $array[$key] = $this->parseDateTimeToString($value);
            }
        }
        return $array;
    }
}
