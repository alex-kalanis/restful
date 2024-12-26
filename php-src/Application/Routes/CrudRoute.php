<?php

namespace kalanis\Restful\Application\Routes;


use kalanis\Restful\Application\IResourceRouter;


/**
 * Resource CrudRoute to simple resource creation
 * @package kalanis\Restful\Routes
 */
class CrudRoute extends ResourceRoute
{

    /** Presenter action names */
    public const ACTION_CREATE = 'create<Relation>';
    public const ACTION_READ = 'read<Relation>';
    public const ACTION_UPDATE = 'update<Relation>';
    public const ACTION_PATCH = 'patch<Relation>';
    public const ACTION_DELETE = 'delete<Relation>';

    /**
     * @param string $mask
     * @param array<string, string|array<string>>|string $metadata
     * @param int $flags
     */
    public function __construct(
        string       $mask,
        array|string $metadata = [],
        int          $flags = IResourceRouter::CRUD
    )
    {
        if (is_string($metadata) && 1 === count(explode(':', $metadata))) {
            $metadata .= ':default';
        }
        parent::__construct($mask, $metadata, $flags);
        $actions = [
            IResourceRouter::POST => self::ACTION_CREATE,
            IResourceRouter::GET => self::ACTION_READ,
            IResourceRouter::PUT => self::ACTION_UPDATE,
            IResourceRouter::PATCH => self::ACTION_PATCH,
            IResourceRouter::DELETE => self::ACTION_DELETE
        ];

        foreach ($actions as $resource => $action) {
            $this->actionDictionary[$resource] = $action;
        }
    }
}
