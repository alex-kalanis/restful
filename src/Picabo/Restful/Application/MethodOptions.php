<?php

namespace Picabo\Restful\Application;

use Nette;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Nette\Routing\Router;
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
        private readonly Router $router,
    )
    {
    }

    /**
     * Get list of available options to given request
     * @return string[]
     */
    public function getOptions(UrlScript $url): array
    {
        return $this->checkAvailableMethods($this->router, $url);
    }

    /**
     * Recursively checks available methods
     * @return string[]
     */
    private function checkAvailableMethods(Router $router, UrlScript $url): array
    {
        $methods = [];
        foreach ($router as $route) {
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

            if ($route instanceof Traversable) {
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

    /**
     * Remove override param from query URL parameters
     * @return string[]
     */
    private function removeOverrideParam(array $query): array
    {
        unset($query['X-HTTP-Method-Override']);
        return $query;
    }
}
