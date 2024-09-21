<?php

namespace Picabo\Restful\Mapping;

use Nette;

/**
 * NullMapper
 * @package Picabo\Restful\Mapping
 * @author Drahomír Hanák
 */
class NullMapper implements IMapper
{
    use Nette\SmartObject;

    /**
     * Convert array or Traversable input to string output response
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = TRUE): string
    {
        return '';
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @return array<string|int, mixed>
     */
    public function parse(mixed $data): array
    {
        return [];
    }
}
