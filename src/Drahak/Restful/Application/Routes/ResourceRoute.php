<?php

namespace Drahak\Restful\Application\Routes;

use Drahak\Restful\Application\IResourceRouter;
use Nette\Application;
use Nette\Application\Routers\Route;
use Nette\Http;
use Nette\Utils\Strings;

/**
 * ResourceRoute
 * @package Drahak\Restful\Routes
 * @author Drahomír Hanák
 *
 * @property array $actionDictionary
 */
class ResourceRoute extends Route implements IResourceRouter
{

    /** @var array */
    protected $actionDictionary;

    /** @var array */
    private $methodDictionary = [Http\IRequest::GET => self::GET, Http\IRequest::POST => self::POST, Http\IRequest::PUT => self::PUT, Http\IRequest::HEAD => self::HEAD, Http\IRequest::DELETE => self::DELETE, 'PATCH' => self::PATCH, 'OPTIONS' => self::OPTIONS];

    /**
     * @param string $mask
     * @param array|string $metadata
     * @param int $flags
     */
    public function __construct($mask, $metadata = [], $flags = IResourceRouter::GET)
    {
        $this->actionDictionary = [];
        if (isset($metadata['action']) && is_array($metadata['action'])) {
            $this->actionDictionary = $metadata['action'];
            $metadata['action'] = 'default';
        } else {
            $action = $metadata['action'] ?? 'default';
            if (is_string($metadata)) {
                $metadataParts = explode(':', $metadata);
                $action = end($metadataParts);
            }
            foreach ($this->methodDictionary as $methodName => $methodFlag) {
                if (($flags & $methodFlag) == $methodFlag) {
                    $this->actionDictionary[$methodFlag] = $action;
                }
            }
        }

        parent::__construct($mask, $metadata, $flags);
    }

    /**
     * Get action dictionary
     * @return array|NULL
     */
    public function getActionDictionary()
    {
        return $this->actionDictionary;
    }

    /**
     * Set action dictionary
     * @param array|NULL
     * @return $this
     */
    public function setActionDictionary($actionDictionary): static
    {
        $this->actionDictionary = $actionDictionary;
        return $this;
    }

    /**
     * @return Application\Request|NULL
     */
    public function match(Http\IRequest $httpRequest): ?array
    {
        $appRequest = parent::match($httpRequest);
        if (!$appRequest) {
            return NULL;
        }

        // Check requested method
        $methodFlag = $this->getMethod($httpRequest);
        if (!$this->isMethod($methodFlag)) {
            return NULL;
        }

        // If there is action dictionary, set method
        if ($this->actionDictionary) {
            $parameters = $appRequest->getParameters();
            $parameters['action'] = $this->actionDictionary[$methodFlag];
            $parameters['action'] = self::formatActionName($this->actionDictionary[$methodFlag], $parameters);
            $appRequest->setParameters($parameters);
        }

        return $appRequest;
    }

    /**
     * Get request method flag
     * @return string|null
     */
    public function getMethod(Http\IRequest $httpRequest)
    {
        $method = $httpRequest->getMethod();
        if (!isset($this->methodDictionary[$method])) {
            return NULL;
        }
        return $this->methodDictionary[$method];
    }

    /**
     * Is this route mapped to given method
     * @param int $method
     */
    public function isMethod($method): bool
    {
        $common = [self::CRUD, self::RESTFUL];
        $isActionDefined = $this->actionDictionary && !in_array($method, $common) ?
            isset($this->actionDictionary[$method]) :
            TRUE;
        return ($this->getFlags() & $method) == $method && $isActionDefined;
    }

    /**
     * Format action name
     * @param string $action
     */
    protected static function formatActionName($action, array $parameters): string
    {
        return Strings::replace($action, "@\<([0-9a-zA-Z_-]+)\>@i", function ($m) use ($parameters) {
            $key = strtolower((string) $m[1]);
            return $parameters[$key] ?? '';
        });
    }

}
