<?php

namespace Drahak\Restful\Converters;

use DateTime;
use DateTimeInterface;
use Nette;
use Traversable;

/**
 * DateTimeConverter
 * @package Drahak\Restful\Converters
 * @author Drahomír Hanák
 */
class DateTimeConverter implements IConverter
{
    use Nette\SmartObject;

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
     */
    public function convert(array $resource): array
    {
        return (array) $this->parseDateTimeToString($resource);
    }

    /**
     * @param mixed $array
     * @return mixed
     */
    private function parseDateTimeToString(mixed $array): mixed
    {
        if (!is_array($array)) {
            if ($array instanceof DateTime || interface_exists('DateTimeInterface') && $array instanceof DateTimeInterface) {
                return $array->format($this->format);
            }
            return $array;
        }

        foreach ($array as $key => $value) {
            if ($value instanceof Traversable || is_array($array)) {
                $array[$key] = $this->parseDateTimeToString($value);
            }
        }
        return $array;
    }

}
