<?php

namespace kalanis\Restful\Mapping;


/**
 * NullMapper
 * @package kalanis\Restful\Mapping
 */
class NullMapper implements IMapper
{

    /**
     * Convert array or Traversable input to string output response
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = true): string
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
