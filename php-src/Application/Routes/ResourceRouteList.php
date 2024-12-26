<?php

namespace kalanis\Restful\Application\Routes;


use kalanis\Restful\Application\IResourceRouter;
use kalanis\Restful\Exceptions\InvalidStateException;
use Nette\Application\Routers\RouteList;


/**
 * ResourceRouteList
 * @package kalanis\Restful\Route
 */
class ResourceRouteList extends RouteList
{

    /**
     * Set offset
     * @param mixed $index
     * @param object $router
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
