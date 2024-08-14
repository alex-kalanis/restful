<?php

namespace Drahak\Restful\Application\Routes;

use Drahak\Restful\Application\IResourceRouter;

/**
 * Resource CrudRoute to simple resource creation
 * @package Drahak\Restful\Routes
 * @author Drahomír Hanák
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
     * @param array|string $metadata
     * @param int $flags
     */
    public function __construct($mask, $metadata = [], $flags = IResourceRouter::CRUD)
    {
        if (is_string($metadata) && count(explode(':', $metadata)) === 1) {
            $metadata .= ':default';
        }
        parent::__construct($mask, $metadata, $flags);
        $actions = [IResourceRouter::POST => self::ACTION_CREATE, IResourceRouter::GET => self::ACTION_READ, IResourceRouter::PUT => self::ACTION_UPDATE, IResourceRouter::PATCH => self::ACTION_PATCH, IResourceRouter::DELETE => self::ACTION_DELETE];

        foreach ($actions as $resource => $action) {
            $this->actionDictionary[$resource] = $action;
        }
    }

}
