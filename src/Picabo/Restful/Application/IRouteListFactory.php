<?php

namespace Picabo\Restful\Application;

use Picabo\Restful\Application\Routes\ResourceRouteList;

/**
 * IRouteListFactory
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
interface IRouteListFactory
{

    /**
     * Create resources route list
     * @param string|null $module
     * @return ResourceRouteList
     */
    public function create(?string $module = NULL): ResourceRouteList;
}
