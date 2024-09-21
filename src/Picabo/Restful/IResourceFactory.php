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
     * @param array<string, mixed> $data
     * @return IResource
     */
    public function create(array $data = []): IResource;
}
