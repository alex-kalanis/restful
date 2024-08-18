<?php

namespace Picabo\Restful\Application;

use Nette\Http;
use Nette\Routing\Router;

/**
 * IResourceRouter
 * @package Picabo\Restful\Routes
 * @author Drahomír Hanák
 */
interface IResourceRouter extends Router
{

    /** Resource methods */
    public const GET = 4;
    public const POST = 8;
    public const PUT = 16;
    public const DELETE = 32;
    public const HEAD = 64;
    public const PATCH = 128;
    public const OPTIONS = 256;

    /** Combined resource methods */
    public const RESTFUL = 508; // GET | POST | PUT | DELETE | HEAD | PATCH | OPTIONS
    public const CRUD = 188; // PUT | GET | POST | DELETE | PATCH

    /**
     * Is this route mapped to given method
     * @param int $method
     * @return bool
     */
    public function isMethod(int $method): bool;

    /**
     * Get request method flag
     * @return string|null
     */
    public function getMethod(Http\IRequest $httpRequest): ?string;

    /**
     * Get action dictionary
     * @return array<string, string> methodFlag => presenterActionName
     */
    public function getActionDictionary(): array;
}
