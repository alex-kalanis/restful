<?php

namespace Drahak\Restful;

use Drahak\Restful\Converters\ResourceConverter;
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
     * @return IResource
     * @throws  InvalidStateException If Accept header is unknown
     */
    public function create(array $data = [])
    {
        return new ConvertedResource($this->resourceConverter, $data);
    }

}
