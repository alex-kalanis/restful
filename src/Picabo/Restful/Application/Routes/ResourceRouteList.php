<?php

namespace Picabo\Restful\Application\Routes;

use Nette\Application\Routers\RouteList;
use Picabo\Restful\Application\IResourceRouter;
use Picabo\Restful\Exceptions\InvalidStateException;

/**
 * ResourceRouteList
 * @package Picabo\Restful\Route
 * @author Drahomír Hanák
 */
class ResourceRouteList extends RouteList
{

    /**
     * Set offset
     * @param mixed $index
     * @param IResourceRouter $router
     *
     * @throws InvalidStateException
     */
    public function offsetSet($index, $router): void
    {
        if (!$router instanceof IResourceRouter && !$router instanceof ResourceRouteList) {
            throw new InvalidStateException('ResourceRouteList expects IResourceRoute, ' . $router::class . ' given.');
        }
        parent::offsetSet($index, $router);
    }
}
