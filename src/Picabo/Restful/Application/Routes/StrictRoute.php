<?php

namespace Picabo\Restful\Application\Routes;

use Nette;
use Nette\Application;
use Nette\Http;
use Nette\Routing\Router;
use Nette\Utils\Strings;
use Picabo\Restful\Exceptions\InvalidArgumentException;
use function str_contains;

/**
 * API strict route
 * - forces URL in form <prefix>/<presenter>[/<relation>[/<relationId>[/<relation>...]]]
 * - constructs app request to <Module>:<Presenter>:read<Relation[0]><Relation[1]>(<RelationId[0]>, <RelationId[1]>, ...)
 * @author Drahomír Hanák
 */
class StrictRoute implements Router
{
    private const
        PresenterKey = 'presenter',
        ModuleKey = 'module';

    use Nette\SmartObject;

    /** method dictionary */
    protected array $methods = [
        Http\IRequest::Get => 'read',
        Http\IRequest::Post => 'create',
        Http\IRequest::Put => 'update',
        Http\IRequest::Delete => 'delete',
        Http\IRequest::Head => 'head',
        'PATCH' => 'patch',
        'OPTIONS' => 'options'
    ];

    public function __construct(
        protected string  $prefix = '',
        protected ?string $module = NULL,
    )
    {
    }

    /**
     * Match request
     */
    public function match(Http\IRequest $httpRequest): ?array
    {
        $path = $httpRequest->url->getPathInfo();
        if (!str_contains($path, $this->prefix)) {
            return NULL;
        }

        $path = Strings::substring($path, strlen($this->prefix) + 1);
        $pathParts = explode('/', $path);
        $pathArguments = array_slice($pathParts, 1);

        $action = $this->getActionName($httpRequest->getMethod(), $pathArguments);
        $params = $this->getPathParameters($pathArguments);
        $params[self::ModuleKey] = $this->module;
        $params[self::PresenterKey] = $pathParts[0];
        $params['action'] = $action;

        $presenter = ($this->module ? $this->module . ':' : '') . $params[self::PresenterKey];

        $appRequest = new Application\Request($presenter, $httpRequest->getMethod(), $params, $httpRequest->getPost(), $httpRequest->getFiles());
        return $appRequest->toArray();
    }

    /**
     * Get action name
     * @param string $method
     * @param array $arguments
     * @return string
     */
    private function getActionName(string $method, array $arguments): string
    {
        if (!isset($this->methods[$method])) {
            throw new InvalidArgumentException(
                'Request method must be one of ' . join(', ', array_keys($this->methods)) . ', ' . $method . ' given'
            );
        }

        $name = $this->methods[$method];
        for ($i = 0, $count = count($arguments); $i < $count; $i += 2) {
            $name .= Strings::firstUpper($arguments[$i]);
        }
        return $name;
    }

    /**
     * Get path parameters
     * @param array $arguments
     * @return array
     */
    private function getPathParameters(array $arguments): array
    {
        $parameters = [];
        for ($i = 1, $count = count($arguments); $i < $count; $i += 2) {
            $parameters[] = $arguments[$i];
        }
        return ['params' => $parameters];
    }

    public function constructUrl(array $params, Nette\Http\UrlScript $refUrl): ?string
    {
        return NULL;
    }
}
