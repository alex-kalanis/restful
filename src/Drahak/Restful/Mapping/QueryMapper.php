<?php

namespace Drahak\Restful\Mapping;

use Drahak\Restful\Mapping\Exceptions\MappingException;
use Nette;
use Traversable;

/**
 * Query string mapper
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
class QueryMapper implements IMapper
{
    use Nette\SmartObject;

    /**
     * Convert array or Traversable input to string output response
     * @param iterable|string $data
     * @param bool $prettyPrint
     */
    public function stringify(iterable|string $data, bool $prettyPrint = TRUE): string
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data, TRUE);
        }
        return http_build_query((array) $data, '', '&');
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @return iterable|string
     *
     * @throws MappingException
     */
    public function parse(mixed $data): iterable|string
    {
        $result = [];
        parse_str(strval($data), $result);
        return $result;
    }
}
