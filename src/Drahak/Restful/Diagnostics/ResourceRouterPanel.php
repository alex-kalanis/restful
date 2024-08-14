<?php

namespace Drahak\Restful\Diagnostics;

use Drahak\Restful\Application\IResourceRouter;
use Nette;
use Nette\Routing\Router;
use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Tracy\IBarPanel;
use Traversable;

/**
 * ResourceRouterPanel to see REST API resource routes
 * @package Drahak\Restful\Diagnostics
 * @author Drahomír Hanák
 */
class ResourceRouterPanel implements IBarPanel
{
    use Nette\SmartObject;

    public function __construct(
        #[\SensitiveParameter] private readonly string $secretKey,
        private readonly string $requestTimeKey,
        private readonly Router $router
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
            ->src(FileSystem::read(__DIR__ . '/icon.png'))
            ->height('16px');
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
        $routes = $this->getResourceRoutes($this->router);
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
        return ob_get_clean();

    }

    private function getResourceRoutes(iterable|Router $routeList): array
    {
        static $resourceRoutes = [];
        foreach ($routeList as $route) {
            if ($route instanceof Traversable)
                $this->getResourceRoutes($route);
            if ($route instanceof IResourceRouter)
                $resourceRoutes[] = $route;
        }
        return $resourceRoutes;
    }
}
