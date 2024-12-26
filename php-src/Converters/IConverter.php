<?php

namespace kalanis\Restful\Converters;


/**
 * Converts resource or input data to some format or stringify objects
 * @package kalanis\Restful\Converters
 * @template TK of string|int
 * @template TVal of mixed
 */
interface IConverter
{
    /**
     * Converts data from resource to output
     * @param array<TK, TVal> $resource
     * @return array<TK, TVal>
     */
    public function convert(array $resource): array;
}
