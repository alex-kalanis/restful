<?php

namespace Drahak\Restful;

use Drahak\Restful\Converters\ResourceConverter;

/**
 * ConvertedResource
 * @package Drahak\Restful
 */
class ConvertedResource extends Resource
{

    public function __construct(private readonly ResourceConverter $resourceConverter, array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Get parsed resource
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        return $this->resourceConverter->convert($data);
    }

}
