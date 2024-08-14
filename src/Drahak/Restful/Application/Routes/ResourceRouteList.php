<?php

namespace Drahak\Restful\Application\Routes;

use Drahak\Restful\Application\IResourceRouter;
use Drahak\Restful\Exceptions\InvalidStateException;
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
