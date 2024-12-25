<?php

namespace Picabo\Restful\Application\Routes;

use Nette\Application;
use Nette\Application\Routers\Route;
use Nette\Http;
use Nette\Utils\Strings;
use Picabo\Restful\Application\IResourceRouter;

/**
 * ResourceRoute
 * @package Picabo\Restful\Routes
 * @author Drahomír Hanák
 */
class ResourceRoute extends Route implements IResourceRouter
{

    /** @var array<int, string> */
    public array $actionDictionary = [];

    /** @var array<string, int> */
    private array $methodDictionary = [
        Http\IRequest::Get => self::GET,
        Http\IRequest::Post => self::POST,
        Http\IRequest::Put => self::PUT,
        Http\IRequest::Head => self::HEAD,
        Http\IRequest::Delete => self::DELETE,
        'PATCH' => self::PATCH,
        'OPTIONS' => self::OPTIONS
    ];

    /**
     * @param string $mask
     * @param array<string, string|array<string>>|string $metadata
     * @param int $flags all available route types with bitwise add
     */
    public function __construct(
        string       $mask,
        array|string $metadata = [],
        int          $flags = IResourceRouter::GET
    )
    {
        if (isset($metadata['action']) && is_array($metadata['action'])) {
            $this->actionDictionary = $metadata['action'];
            $metadata['action'] = 'default';
        } else {
            $action = is_array($metadata) && !empty($metadata['action']) ? strval($metadata['action']) : 'default';
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

        parent::__construct($mask, $metadata);
    }

    /**
     * Get action dictionary
     * @return array<int, string>
     */
    public function getActionDictionary(): array
    {
        return $this->actionDictionary;
    }

    /**
     * Set action dictionary
     * @param array<int, string> $actionDictionary
     * @return $this
     */
    public function setActionDictionary(array $actionDictionary): static
    {
        $this->actionDictionary = $actionDictionary;
        return $this;
    }

    /**
     * @param Http\IRequest $httpRequest
     * @return array<string, mixed>|null
     */
    public function match(Http\IRequest $httpRequest): ?array
    {
        $appRequest = parent::match($httpRequest);
        if (is_null($appRequest)) {
            return NULL;
        }

        // Check requested method
        $methodFlag = $this->getMethod($httpRequest);
        if (is_null($methodFlag) || !$this->isMethod($methodFlag)) {
            return NULL;
        }

        // If there is action dictionary, set method
        if ($this->actionDictionary) {
            $appRequest['action'] = $this->actionDictionary[$methodFlag];
            $appRequest['action'] = self::formatActionName($this->actionDictionary[$methodFlag], $appRequest);
        }

        return $appRequest;
    }

    /**
     * Get request method flag
     */
    public function getMethod(Http\IRequest $httpRequest): ?int
    {
        $method = strtoupper($httpRequest->getMethod());
        if (!isset($this->methodDictionary[$method])) {
            return NULL;
        }
        return $this->methodDictionary[$method];
    }

    /**
     * Is this route mapped to given method
     */
    public function isMethod(int $method): bool
    {
        $common = [self::CRUD, self::RESTFUL];
        $isActionDefined = $this->actionDictionary && !in_array($method, $common)
            ? isset($this->actionDictionary[$method])
            : TRUE;
//        return ($this->getFlag() & $method) == $method && $isActionDefined; // getFlag je dneska jinde
        return $isActionDefined;
    }

    /**
     * Format action name
     * @param string $action
     * @param array<string|int, mixed> $parameters
     * @return string
     */
    protected static function formatActionName(string $action, array $parameters): string
    {
        return Strings::replace($action, "@<([0-9a-zA-Z_-]+)>@i", function ($m) use ($parameters) {
            $key = strtolower((string) $m[1]);
            return $parameters[$key] ?? '';
        });
    }
}
