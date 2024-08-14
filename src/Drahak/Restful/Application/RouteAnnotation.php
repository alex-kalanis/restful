<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\Exceptions\InvalidArgumentException;
use Nette;
use Nette\Application\UI\MethodReflection;
use Nette\Http\IRequest;
use ReflectionMethod;
use Reflector;

/**
 * RouteAnnotation
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 *
 * @property-read string[] $methods
 */
class RouteAnnotation implements IAnnotationParser
{
    use Nette\SmartObject;

    /** @var array */
    private array $methods = [
        IRequest::Get => IResourceRouter::GET,
        IRequest::Post => IResourceRouter::POST,
        IRequest::Put => IResourceRouter::PUT,
        IRequest::Delete => IResourceRouter::DELETE,
        IRequest::Head => IResourceRouter::HEAD,
        'PATCH' => IResourceRouter::PATCH
    ];

    /**
     * Get parsed
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * @param ReflectionMethod $reflection
     *
     * @throws InvalidArgumentException
     */
    public function parse(Reflector $reflection): array
    {
        if (!$reflection instanceof MethodReflection) {
            throw new InvalidArgumentException('RouteAnnotation can be parsed only on method from Nette\Application\UI\MethodReflection');
        }

        $result = [];
        foreach ($this->methods as $methodName => $methodFlag) {
            if ($reflection->hasAnnotation($methodName)) {
                $result[$methodFlag] = $reflection->getAnnotation($methodName);
            }
        }
        return $result;
    }
}
