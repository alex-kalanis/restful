<?php

namespace Picabo\Restful\Application;

use Nette;
use Nette\Application\UI;
use Nette\Loaders\RobotLoader;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Utils\Strings;
use ReflectionMethod;
use function str_contains;

/**
 * RouteListFactory
 * @package Picabo\Restful\Application\Routes
 * @author Drahomír Hanák
 */
class RouteListFactory implements IRouteListFactory
{
    use Nette\SmartObject;

    private RobotLoader $loader;

    private string $module = '';

    private string $prefix = '';

    private string $cacheDirectory = '';

    /**
     * @param string $presentersRoot from where to find presenters
     * @param bool $autoRebuild enable automatic rebuild of robot loader
     * @param string $cacheDirectory directory where to cache
     */
    public function __construct(
        string                           $presentersRoot,
        bool                             $autoRebuild,
        string                           $cacheDirectory,
        private readonly RouteAnnotation $routeAnnotation,
    )
    {
        $loader = new RobotLoader();
        $loader->addDirectory($presentersRoot);
        $loader->setTempDirectory($cacheDirectory);
        $loader->register();
        $loader->setAutoRefresh($autoRebuild);
        $loader->tryLoad(IResourcePresenter::class);

        $this->loader = $loader;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * Set default module of created routes
     * @param string $module
     * @return $this
     */
    public function setModule(string $module): self
    {
        $this->module = $module;
        return $this;
    }

    /**
     * Set default routes URL mask prefix
     * @param string $prefix
     * @return $this
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Create route list
     * @param string|null $module
     * @return Routes\ResourceRouteList
     */
    public final function create(?string $module = NULL): Routes\ResourceRouteList
    {
        $routeList = new Routes\ResourceRouteList($module ?: $this->module);
        foreach ($this->loader->getIndexedClasses() as $class => $file) {
            /** @var class-string<object> $class */
            try {
                self::getClassReflection($class);
            } catch (\ReflectionException) {
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
     * @param class-string<object> $className
     * @return UI\ComponentReflection
     * @throws \ReflectionException
     */
    private static function getClassReflection(string $className): UI\ComponentReflection
    {
        return new UI\ComponentReflection($className);
    }

    /**
     * Get class methods
     * @param class-string<object> $className
     * @return ReflectionMethod[]
     * @throws InvalidStateException
     */
    protected function getClassMethods(string $className): array
    {
        return self::getClassReflection($className)->getMethods();
    }

    /**
     * Parse route annotations on given object methods
     * @param ReflectionMethod[] $methods
     * @return array<string, array<string, string>> $data[URL mask][request method] = action name e.g. $data['api/v1/articles']['GET'] = 'read'
     */
    protected function parseClassRoutes(array $methods): array
    {
        $routeData = [];
        foreach ($methods as $method) {
            // Parse annotations only on action methods
            if (!str_contains($method->getName(), 'action'))
                continue;

            $annotations = $this->routeAnnotation->parse($method);
            foreach ($annotations as $requestMethod => $mask) {
                $action = str_replace('action', '', $method->getName());
                $action = Strings::firstLower($action);

                $pattern = $this->prefix ?
                    $this->prefix . '/' . $mask :
                    $mask;

                $routeData[strval($pattern)][strval($requestMethod)] = $action;
            }
        }
        return $routeData;
    }

    /**
     * Add class routes to route list
     * @param Routes\ResourceRouteList $routeList
     * @param array<string, array<string, string>> $routeData
     * @param class-string<object> $className
     * @return Routes\ResourceRouteList
     */
    protected function addRoutes(Routes\ResourceRouteList $routeList, array $routeData, string $className): Routes\ResourceRouteList
    {
        $presenter = str_replace('Presenter', '', self::getClassReflection($className)->getShortName());
        foreach ($routeData as $mask => $dictionary) {
            $routeList[] = new Routes\ResourceRoute($mask, ['presenter' => $presenter, 'action' => $dictionary], IResourceRouter::RESTFUL);
        }
        return $routeList;
    }
}
