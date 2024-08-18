<?php

namespace Picabo\Restful;

/**
 * IResourceFactory
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
interface IResourceFactory
{

    /**
     * Create new API resource
     * @return IResource
     */
    public function create(array $data = []): IResource;
}
