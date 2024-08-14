<?php

namespace Drahak\Restful\Application;

use Nette;
use Nette\Application\IRouter;
use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\UrlScript;
use Traversable;

/**
 * MethodOptions
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 */
class MethodOptions
{
    use Nette\SmartObject;

    /** @var array */
    private $methods = [IResourceRouter::GET => IRequest::GET, IResourceRouter::POST => IRequest::POST, IResourceRouter::PUT => IRequest::PUT, IResourceRouter::DELETE => IRequest::DELETE, IResourceRouter::HEAD => IRequest::HEAD, IResourceRouter::PATCH => 'PATCH', IResourceRouter::OPTIONS => 'OPTIONS'];

    public function __construct(private IRouter $router)
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
    private function checkAvailableMethods(IRouter $router, UrlScript $url): array
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
     * @return int|NULL
     */
    protected function getMethodFlag(IResourceRouter $route)
    {
        $methodFlag = NULL;
        foreach ($this->methods as $flag => $requestMethod) {
            if ($route->isMethod($flag)) {
                return $flag;
            }
        }
        return $methodFlag;
    }

    /**
     * Create route acceptable HTTP request
     * @param int $methodFlag
     * @return Request
     */
    protected function createAcceptableRequest(UrlScript $url, $methodFlag)
    {
        return new Request(
            $url,
            NULL, NULL, NULL, NULL, NULL,
            $this->methods[$methodFlag]
        );
    }

    /**
     * Remove override param from query URL parameters
     * @return string
     */
    private function removeOverrideParam(array $query)
    {
        unset($query['X-HTTP-Method-Override']);
        return $query;
    }

}
