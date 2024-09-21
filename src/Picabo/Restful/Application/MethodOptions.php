<?php

namespace Picabo\Restful\Application;

use Nette;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Routing\Router;
use Nette\Routing\RouteList;
use Traversable;

/**
 * MethodOptions
 * @package Picabo\Restful\Application
 * @author Drahomír Hanák
 */
class MethodOptions
{
    use Nette\SmartObject;

    /** @var array<int, string> */
    private array $methods = [
        IResourceRouter::GET => IRequest::Get,
        IResourceRouter::POST => IRequest::Post,
        IResourceRouter::PUT => IRequest::Put,
        IResourceRouter::DELETE => IRequest::Delete,
        IResourceRouter::HEAD => IRequest::Head,
        IResourceRouter::PATCH => 'PATCH',
        IResourceRouter::OPTIONS => 'OPTIONS',
    ];

    public function __construct(
        private readonly Router|RouteList $router,
    )
    {
    }

    /**
     * Get list of available options to given request
     * @return string[]
     */
    public function getOptions(UrlScript $url): array
    {
        $router = is_a($this->router, RouteList::class)
            ? $this->router
            : (new RouteList())->add($this->router);
        ;
        return $this->checkAvailableMethods($router, $url);
    }

    /**
     * Recursively checks available methods
     * @return string[]
     */
    private function checkAvailableMethods(Nette\Routing\RouteList $router, UrlScript $url): array
    {
        $methods = [];
        foreach ($router->getRouters() as $route) {
            if ($route instanceof IResourceRouter && !$route instanceof Traversable) {
                $methodFlag = $this->getMethodFlag($route);
                if (!$methodFlag) continue;

                $request = $this->createAcceptableRequest($url, $methodFlag);

                $acceptableMethods = array_keys($route->getActionDictionary());
                $methodNames = [];
                foreach ($acceptableMethods as $flag) {
                    $methodNames[] = $this->methods[$flag];
                }

                if (in_array($route->getMethod($request), $acceptableMethods) && $route->match($request)) {
                    return $methodNames;
                }
            }

            if (is_a($route, Nette\Routing\RouteList::class)) {
                $newMethods = $this->checkAvailableMethods($route, $url);
                $methods = array_merge($methods, $newMethods);
            }
        }
        return $methods;
    }

    /**
     * Get route method flag
     */
    protected function getMethodFlag(IResourceRouter $route): ?int
    {
        foreach ($this->methods as $flag => $requestMethod) {
            if ($route->isMethod($flag)) {
                return $flag;
            }
        }
        return null;
    }

    /**
     * Create route acceptable HTTP request
     */
    protected function createAcceptableRequest(UrlScript $url, int $methodFlag): Request
    {
        return new Request(
            $url,
            [], [], [], [],
            $this->methods[$methodFlag]
        );
    }
}
