<?php

namespace Picabo\Restful\Application;

use Picabo\Restful\IResource;

/**
 * IResponseFactory
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
interface IResponseFactory
{

    /**
     * Create new API response
     */
    public function create(IResource $resource, ?string $contentType = NULL): Responses\IResponse;
}
