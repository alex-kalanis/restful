<?php

namespace kalanis\Restful\Application;


use kalanis\Restful\IResource;


/**
 * IResponseFactory
 * @package kalanis\Restful\Application
 */
interface IResponseFactory
{

    /**
     * Create new API response
     */
    public function create(IResource $resource, ?string $contentType = null): \kalanis\Restful\Application\Responses\IResponse;
}
