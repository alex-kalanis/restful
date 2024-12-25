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
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = TRUE): string;

    /**
     * Convert client request data to array or traversable
     * @throws MappingException
     * @return object|string|array<string|int, mixed>|Traversable<string|int, mixed>
     */
    public function parse(mixed $data): iterable|string|object;
}
