<?php

namespace Picabo\Restful\Mapping;

use Nette;
use Picabo\Restful\Mapping\Exceptions\MappingException;
use Traversable;

/**
 * Query string mapper
 * @package Picabo\Restful\Mapping
 * @author Drahomír Hanák
 */
class QueryMapper implements IMapper
{
    use Nette\SmartObject;

    /**
     * Convert array or Traversable input to string output response
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = TRUE): string
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        return http_build_query((array) $data, '', '&');
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @return iterable<string|int, mixed>
     *
     * @throws MappingException
     */
    public function parse(mixed $data): iterable
    {
        $result = [];
        parse_str(strval($data), $result);
        return $result;
    }
}
