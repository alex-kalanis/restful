<?php

namespace Drahak\Restful\Application;

use Nette\Application\IRouter;
use Nette\Http;

/**
 * IResourceRouter
 * @package Drahak\Restful\Routes
 * @author Drahomír Hanák
 */
interface IResourceRouter extends IRouter
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
    public function isMethod($method);

    /**
     * Get request method flag
     * @return string|null
     */
    public function getMethod(Http\IRequest $httpRequest);

    /**
     * Get action dictionary
     * @return array methodFlag => presenterActionName
     */
    public function getActionDictionary();


}
