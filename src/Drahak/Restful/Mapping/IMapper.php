<?php

namespace Drahak\Restful\Mapping;

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
     * @param array|Traversable $data
     * @param bool $prettyPrint
     * @return mixed
     *
     */
    public function stringify($data, $prettyPrint = TRUE);

    /**
     * Convert client request data to array or traversable
     * @return array|Traversable
     * @throws MappingException
     */
    public function parse(mixed $data);

}