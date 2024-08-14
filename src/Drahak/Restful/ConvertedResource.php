<?php

namespace Drahak\Restful;

use Drahak\Restful\Converters\ResourceConverter;

/**
 * ConvertedResource
 * @package Drahak\Restful
 */
class ConvertedResource extends Resource
{
    public function __construct(
        private readonly ResourceConverter $resourceConverter,
        array $data = [],
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
