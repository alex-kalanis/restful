<?php

namespace kalanis\Restful\Mapping;


use kalanis\Restful\Mapping\Exceptions\MappingException;
use Traversable;


/**
 * Query string mapper
 * @package kalanis\Restful\Mapping
 */
class QueryMapper implements IMapper
{

    /**
     * Convert array or Traversable input to string output response
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = true): string
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        return http_build_query((array) $data, '', '&');
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @throws MappingException
     * @return iterable<string|int, mixed>
     *
     */
    public function parse(mixed $data): iterable
    {
        $result = [];
        parse_str(strval($data), $result);
        return $result;
    }
}
