<?php

namespace Drahak\Restful\Application\Routes;

use Drahak\Restful\Application\IResourceRouter;
use Drahak\Restful\InvalidStateException;
use Nette\Application\Routers\RouteList;

/**
 * ResourceRouteList
 * @package Drahak\Restful\Route
 * @author Drahomír Hanák
 */
class ResourceRouteList extends RouteList
{

    /**
     * Set offset
     * @param mixed $index
     * @param IResourceRouter $route
     *
     * @throws InvalidStateException
     */
    public function offsetSet($index, $route): void
    {
        if (!$route instanceof IResourceRouter && !$route instanceof ResourceRouteList) {
            throw new InvalidStateException('ResourceRouteList expects IResourceRoute, ' . $route::class . ' given.');
        }
        parent::offsetSet($index, $route);
    }

}
