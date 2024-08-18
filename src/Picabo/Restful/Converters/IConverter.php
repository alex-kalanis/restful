<?php

namespace Picabo\Restful\Converters;

/**
 * Converts resource or input data to some format or stringify objects
 * @package Picabo\Restful\Converters
 */
interface IConverter
{
    /**
     * Converts data from resource to output
     */
    public function convert(array $resource): array;
}
