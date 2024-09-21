<?php

namespace Picabo\Restful\Diagnostics;

use Nette;
use Nette\Routing\Router;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Picabo\Restful\Application\IResourceRouter;
use Tracy\IBarPanel;
use Traversable;

/**
 * ResourceRouterPanel to see REST API resource routes
 * @package Picabo\Restful\Diagnostics
 * @author Drahomír Hanák
 */
class ResourceRouterPanel implements IBarPanel
{
    use Nette\SmartObject;

    public function __construct(
        #[\SensitiveParameter] private readonly string $secretKey,
        private readonly string                        $requestTimeKey,
        private readonly Router                        $router
    )
    {
    }

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    public function getTab(): string
    {
        $icon = Html::el('img')
            ->src('data:image/png;base64,' . base64_encode(FileSystem::read(__DIR__ . '/icon.png')))
            ->height(16);
        return '<span class="REST API resource routes">' . $icon . 'API resources</span>';
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    public function getPanel(): string
    {
        ob_start();
        $esc = ['Nette\Templating\Helpers', 'escapeHtml'];
        $router = ($this->router instanceof Nette\Routing\RouteList)
            ? $this->router
            : (new Nette\Routing\RouteList())->add($this->router);
        $routes = $this->getResourceRoutes($router);
        $methods = [
            IResourceRouter::GET => 'GET',
            IResourceRouter::POST => 'POST',
            IResourceRouter::PUT => 'PUT',
            IResourceRouter::DELETE => 'DELETE',
            IResourceRouter::HEAD => 'HEAD',
            IResourceRouter::PATCH => 'PATCH',
            IResourceRouter::OPTIONS => 'OPTIONS',
        ];
        $privateKey = $this->secretKey;
        $requestTimeKey = $this->requestTimeKey;

        require_once __DIR__ . '/panel.phtml';
        return strval(ob_get_clean());
    }

    /**
     * @param Nette\Routing\RouteList|iterable<object> $routeList
     * @return array<IResourceRouter>
     */
    private function getResourceRoutes(iterable|Nette\Routing\RouteList $routeList): array
    {
        static $resourceRoutes = [];
        $iter = is_object($routeList) && is_a($routeList, Nette\Routing\RouteList::class)
            ? $routeList->getRouters()
            : $routeList
        ;
        foreach ($iter as $route) {
            if ($route instanceof Traversable) {
                $this->getResourceRoutes($route);
            }
            if ($route instanceof IResourceRouter) {
                $resourceRoutes[] = $route;
            }
        }
        return $resourceRoutes;
    }
}
