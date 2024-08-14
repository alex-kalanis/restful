<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\IResource;

/**
 * IResponseFactory
 * @package Drahak\Restful
 * @author Drahomír Hanák
 */
interface IResponseFactory
{

    /**
     * Create new API response
     */
    public function create(IResource $resource): Responses\IResponse;
}
