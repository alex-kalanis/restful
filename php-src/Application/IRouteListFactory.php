<?php

namespace kalanis\Restful\Application;


use kalanis\Restful\Application\Routes\ResourceRouteList;


/**
 * IRouteListFactory
 * @package kalanis\Restful\Application
 */
interface IRouteListFactory
{

    /**
     * Create resources route list
     * @param string|null $module
     * @return ResourceRouteList
     */
    public function create(?string $module = null): ResourceRouteList;
}
