<?php

namespace Drahak\Restful\Mapping;

use Drahak\Restful\Mapping\Exceptions\MappingException;
use Traversable;

/**
 * Resource data mapper interface
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
interface IMapper
{

    /**
     * Convert array or Traversable input to string output response
     * @param iterable|string $data
     * @param bool $prettyPrint
     * @return string
     */
    public function stringify(iterable|string $data, bool $prettyPrint = TRUE): string;

    /**
     * Convert client request data to array or traversable
     * @return array|Traversable
     * @throws MappingException
     */
    public function parse(mixed $data): iterable|string;
}
