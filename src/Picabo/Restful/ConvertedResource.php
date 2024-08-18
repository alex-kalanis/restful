<?php

namespace Picabo\Restful;

use Picabo\Restful\Converters\ResourceConverter;

/**
 * ConvertedResource
 * @package Picabo\Restful
 */
class ConvertedResource extends Resource
{
    public function __construct(
        private readonly ResourceConverter $resourceConverter,
        array                              $data = [],
    )
    {
        parent::__construct($data);
    }

    /**
     * Get parsed resource
     */
    public function getData(): array
    {
        return $this->resourceConverter->convert(parent::getData());
    }
}
