<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\Application\Routes\ResourceRoute;
use Drahak\Restful\Application\Routes\ResourceRouteList;
use Drahak\Restful\InvalidStateException;
use Drahak\Restful\Utils\Strings;
use Nette;
use Nette\Loaders\RobotLoader;
use Nette\Reflection\ClassType;
use Nette\Reflection\Method;

/**
 * RouteListFactory
 * @package Drahak\Restful\Application\Routes
 * @author Drahomír Hanák
 *
 * @property-write string $module
 * @property-write string $prefix
 */
class RouteListFactory implements IRouteListFactory
{
    use Nette\SmartObject;

    /** @var RobotLoader */
    private $loader;

    /** @var string */
    private $module;

    /** @var string */
    private $prefix;

    /** @var string */
    private $cacheDirectory;

    /**
     * @param string $presentersRoot from where to find presenters
     * @param bool $autoRebuild enable automatic rebuild of robot loader
     * @param string $cacheDirectory directory where to cache
     */
    public function __construct($presentersRoot, $autoRebuild, $cacheDirectory, private RouteAnnotation $routeAnnotation)
    {
        $loader = new RobotLoader();
        $loader->addDirectory($presentersRoot);
        $loader->setTempDirectory($cacheDirectory);
        $loader->register();
        $loader->setAutoRefresh($autoRebuild);
        $loader->tryLoad(\Drahak\Restful\Application\IResourcePresenter::class);

        $this->loader = $loader;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Set default module of created routes
     * @param string $module
     * @return ResourceRoute
     */
    public function setModule($module): static
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Set default routes URL mask prefix
     * @param string $prefix
     * @return RouteListFactory
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $prefix;
    }

    /**
     * Create route list
     * @param string|null $module
     * @return ResourceRouteList
     */
    public final function create($module = NULL)
    {
        $routeList = new ResourceRouteList($module ?: $this->module);
        foreach ($this->loader->getIndexedClasses() as $class => $file) {
            try {
                self::getClassReflection($class);
            } catch (InvalidStateException) {
                continue;
            }

            $methods = $this->getClassMethods($class);
            $routeData = $this->parseClassRoutes($methods);
            $this->addRoutes($routeList, $routeData, $class);
        }
        return $routeList;
    }

    /******************** Template methods ********************/

    /**
     * Get class reflection
     * @param string $className
     * @return ClassType
     *
     * @throws InvalidStateException
     */
    private static function getClassReflection($className)
    {
        $class = class_exists('Nette\Reflection\ClassType') ? 'Nette\Reflection\ClassType' : 'ReflectionClass';
        return new $class($className);
    }

    /**
     * Get class methods
     * @param string $className
     * @return Method[]
     *
     * @throws InvalidStateException
     */
    protected function getClassMethods($className)
    {
        return self::getClassReflection($className)->getMethods();
    }

    /**
     * Parse route annotations on given object methods
     * @param Method[] $methods
     * @return array $data[URL mask][request method] = action name e.g. $data['api/v1/articles']['GET'] = 'read'
     */
    protected function parseClassRoutes($methods): array
    {
        $routeData = [];
        foreach ($methods as $method) {
            // Parse annotations only on action methods
            if (!\str_contains((string) $method->getName(), 'action'))
                continue;

            $annotations = $this->routeAnnotation->parse($method);
            foreach ($annotations as $requestMethod => $mask) {
                $action = str_replace('action', '', $method->getName());
                $action = Strings::firstLower($action);

                $pattern = $this->prefix ?
                    $this->prefix . '/' . $mask :
                    $mask;

                $routeData[$pattern][$requestMethod] = $action;
            }
        }
        return $routeData;
    }

    /**
     * Add class routes to route list
     * @param string $className
     * @return ResourceRouteList
     *
     * @throws InvalidStateException
     */
    protected function addRoutes(ResourceRouteList $routeList, array $routeData, $className)
    {
        $presenter = str_replace('Presenter', '', self::getClassReflection($className)->getShortName());
        foreach ($routeData as $mask => $dictionary) {
            $routeList[] = new ResourceRoute($mask, ['presenter' => $presenter, 'action' => $dictionary], IResourceRouter::RESTFUL);
        }
        return $routeList;
    }

}
