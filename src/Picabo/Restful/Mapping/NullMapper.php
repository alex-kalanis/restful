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
     */
    public function stringify(iterable|string $data, bool $prettyPrint = TRUE): string
    {
        return '';
    }

    /**
     * Convert client request data to array or traversable
     */
    public function parse(mixed $data): iterable|string
    {
        return [];
    }
}
