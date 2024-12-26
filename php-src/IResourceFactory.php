<?php

namespace kalanis\Restful;


/**
 * IResourceFactory
 * @package kalanis\Restful
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
