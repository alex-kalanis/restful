<?php

namespace Drahak\Restful;

use Drahak\Restful\Converters\ResourceConverter;
use Drahak\Restful\Exceptions\InvalidStateException;
use Nette;
use Nette\Http\IRequest;

/**
 * ResourceFactory
 * @package Drahak\Restful
 * @author Drahomír Hanák
 */
class ResourceFactory implements IResourceFactory
{
    use Nette\SmartObject;

    public function __construct(private IRequest $request, private ResourceConverter $resourceConverter)
    {
    }

    /**
     * Create new API resource
     * @param array $data
     * @return IResource
     * @throws InvalidStateException If Accept header is unknown
     */
    public function create(array $data = []): IResource
    {
        return new ConvertedResource($this->resourceConverter, $data);
    }
}
