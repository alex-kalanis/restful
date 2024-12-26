<?php

namespace kalanis\Restful\Application;


use kalanis\Restful\Application\Routes\ResourceRouteList;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\Utils\Finder;


/**
 * CachedRouteListFactory
 * @package kalanis\Restful\Application
 */
final class CachedRouteListFactory implements IRouteListFactory
{

    /** Cache name */
    public const CACHE_NAME = 'resourceRouteList';

    private Cache $cache;

    public function __construct(
        private readonly string            $presentersRoot,
        private readonly IRouteListFactory $routeListFactory,
        Storage                            $storage,
    )
    {
        $this->cache = new Cache($storage, self::class);
    }

    /**
     * Create cached route list
     */
    private function createCached(?string $module = null): ResourceRouteList
    {
        $files = [];
        $presenterFiles = Finder::findFiles('*Presenter.php')->from($this->presentersRoot);
        foreach ($presenterFiles as $path => $splFile) {
            $files[] = $path;
        }

        $routeList = $this->routeListFactory->create($module);
        $this->cache->save(self::CACHE_NAME, $routeList, [Cache::Files => $files]);
        return $routeList;
    }

    /**
     * Create resources route list
     */
    public function create(?string $module = null): ResourceRouteList
    {
        $routeList = $this->cache->load(self::CACHE_NAME);
        if (is_object($routeList) && is_a($routeList, ResourceRouteList::class)) {
            return $routeList;
        }
        return $this->createCached($module);
    }
}
