<?php

namespace Picabo\Restful\Application;

use Nette;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\Utils\Finder;
use Picabo\Restful\Application\Routes\ResourceRouteList;

/**
 * CachedRouteListFactory
 * @package Picabo\Restful\Application\Routes
 * @author Drahomír Hanák
 */
final class CachedRouteListFactory implements IRouteListFactory
{
    use Nette\SmartObject;

    /** Cache name */
    public const CACHE_NAME = 'resourceRouteList';

    private Cache $cache;

    public function __construct(
        private readonly string            $presentersRoot,
        private readonly IRouteListFactory $routeListFactory,
        Storage                            $storage
    )
    {
        $this->cache = new Cache($storage, self::class);
    }

    /**
     * Create cached route list
     */
    private function createCached(?string $module = NULL): ResourceRouteList
    {
        $files = [];
        $presenterFiles = Finder::findFiles('*Presenter.php')->from($this->presentersRoot);
        foreach ($presenterFiles as $path => $splFile) {
            $files[] = $path;
        }

        $routeList = $this->routeListFactory->create($module);
        $this->cache->save(self::CACHE_NAME, $routeList, [Cache::FILES => $files]);
        return $routeList;
    }

    /******************** Route list factory ********************/

    /**
     * Create resources route list
     */
    public function create(?string $module = NULL): ResourceRouteList
    {
        $routeList = $this->cache->load(self::CACHE_NAME);
        if (is_object($routeList) && is_a($routeList, ResourceRouteList::class)) {
            return $routeList;
        }
        return $this->createCached($module);
    }
}
