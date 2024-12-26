<?php

namespace kalanis\Restful;


use kalanis\Restful\Converters\ResourceConverter;
use kalanis\Restful\Exceptions\InvalidStateException;


/**
 * ResourceFactory
 * @package kalanis\Restful
 */
class ResourceFactory implements IResourceFactory
{
    public function __construct(
        private readonly ResourceConverter $resourceConverter,
    )
    {
    }

    /**
     * Create new API resource
     * @param array<string, mixed> $data
     * @throws InvalidStateException If Accept header is unknown
     * @return IResource
     */
    public function create(array $data = []): IResource
    {
        return new ConvertedResource($this->resourceConverter, $data);
    }
}
