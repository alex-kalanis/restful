<?php

namespace kalanis\Restful\Converters;


use kalanis\Restful\IResource;
use stdClass;
use Traversable;


/**
 * ObjectConverter
 * @package kalanis\Restful\Converters
 * @template TK of string
 * @template TVal of mixed
 * @implements IConverter<TK, TVal>
 */
class ObjectConverter implements IConverter
{

    /**
     * Converts stdClass and traversable objects in resource to array
     * @param array<TK, TVal> $resource
     * @return array<TK, TVal>
     */
    public function convert(array $resource): array
    {
        return (array) $this->parseObjects($resource);
    }

    /**
     * Parse objects in resource
     * @param array<TK, TVal>|Traversable<TK, TVal>|stdClass $data
     * @return array<TK, TVal>
     */
    protected function parseObjects(array|object $data): array
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        } else if ($data instanceof stdClass) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof IResource) {
                $value = $value->getData();
            }

            if ($value instanceof Traversable || $value instanceof stdClass || is_array($value)) {
                $data[$key] = $this->parseObjects($value);
            }
        }
        return $data;
    }
}
