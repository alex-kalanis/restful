<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\Application\Routes\ResourceRouteList;
use Nette;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Utils\Finder;

/**
 * CachedRouteListFactory
 * @package Drahak\Restful\Application\Routes
 * @author Drahomír Hanák
 */
final class CachedRouteListFactory implements IRouteListFactory
{
    use Nette\SmartObject;

    /** Cache name */
    public const CACHE_NAME = 'resourceRouteList';

    /** @var Cache */
    private $cache;

    /**
     * @param string $presentersRoot
     */
    public function __construct(private $presentersRoot, private IRouteListFactory $routeListFactory, IStorage $storage)
    {
        $this->cache = new Cache($storage, self::class);
    }

    /**
     * Create cached route list
     * @return ResourceRouteList
     */
    private function createCached($module = NULL)
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
     * @param string|null $module
     * @return ResourceRouteList
     */
    public function create($module = NULL)
    {
        $routeList = $this->cache->load(self::CACHE_NAME);
        if ($routeList !== NULL) {
            return $routeList;
        }
        return $this->createCached($module);
    }

}
