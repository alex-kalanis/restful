<?php

namespace Picabo\Restful\Mapping;

use Picabo\Restful\Mapping\Exceptions\MappingException;
use Traversable;

/**
 * Resource data mapper interface
 * @package Picabo\Restful\Mapping
 * @author Drahomír Hanák
 */
interface IMapper
{

    /**
     * Convert array or Traversable input to string output response
     * @param iterable|string|object $data
     * @param bool $prettyPrint
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = TRUE): string;

    /**
     * Convert client request data to array or traversable
     * @return array|Traversable|object
     * @throws MappingException
     */
    public function parse(mixed $data): iterable|string|object;
}
