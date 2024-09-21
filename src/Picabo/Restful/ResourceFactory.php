<?php

namespace Picabo\Restful;

use Nette;
use Nette\Http\IRequest;
use Picabo\Restful\Converters\ResourceConverter;
use Picabo\Restful\Exceptions\InvalidStateException;

/**
 * ResourceFactory
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
class ResourceFactory implements IResourceFactory
{
    use Nette\SmartObject;

    public function __construct(private ResourceConverter $resourceConverter)
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
