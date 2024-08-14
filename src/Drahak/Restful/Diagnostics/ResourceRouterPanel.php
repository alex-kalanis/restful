<?php

namespace Drahak\Restful\Diagnostics;

use Drahak\Restful\Application\IResourceRouter;
use Nette;
use Nette\Application\IRouter;
use Nette\Templating\Helpers;
use Nette\Utils\Html;
use Tracy\IBarPanel;
use Traversable;

if (!interface_exists(\Tracy\IBarPanel::class)) {
    class_alias('Nette\Diagnostics\IBarPanel', \Tracy\IBarPanel::class);
}

/**
 * ResourceRouterPanel to see REST API resource routes
 * @package Drahak\Restful\Diagnostics
 * @author Drahomír Hanák
 */
class ResourceRouterPanel implements IBarPanel
{
    use Nette\SmartObject;

    /**
     * @param string $secretKey
     * @param string $requestTimeKey
     */
    public function __construct(private $secretKey, private $requestTimeKey, private IRouter $router)
    {
    }

    /**
     * Renders HTML code for custom tab.
     * @return string
     */
    public function getTab()
    {
        $icon = Html::el('img')
            ->src(Helpers::dataStream(file_get_contents(__DIR__ . '/icon.png')))
            ->height('16px');
        return '<span class="REST API resource routes">' . $icon . 'API resources</span>';
    }

    /**
     * Renders HTML code for custom panel.
     * @return string
     */
    public function getPanel()
    {
        ob_start();
        $esc = ['Nette\Templating\Helpers', 'escapeHtml'];
        $routes = $this->getResourceRoutes($this->router);
        $methods = [IResourceRouter::GET => 'GET', IResourceRouter::POST => 'POST', IResourceRouter::PUT => 'PUT', IResourceRouter::DELETE => 'DELETE', IResourceRouter::HEAD => 'HEAD', IResourceRouter::PATCH => 'PATCH', IResourceRouter::OPTIONS => 'OPTIONS'];
        $privateKey = $this->secretKey;
        $requestTimeKey = $this->requestTimeKey;

        require_once __DIR__ . '/panel.phtml';
        return ob_get_clean();

    }

    /**
     * @param $routeList
     * @return array
     */
    private function getResourceRoutes($routeList)
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
