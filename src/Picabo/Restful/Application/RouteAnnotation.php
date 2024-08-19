<?php

namespace Picabo\Restful\Application;

use Nette;
use Nette\Application\UI\MethodReflection;
use Nette\Http\IRequest;
use Picabo\Restful\Exceptions\InvalidArgumentException;
use ReflectionMethod;
use Reflector;

/**
 * RouteAnnotation
 * @package Picabo\Restful\Application
 * @author Drahomír Hanák
 *
 * @property-read array<string, int> $methods
 */
class RouteAnnotation implements IAnnotationParser
{
    use Nette\SmartObject;

    /** @var array<string, int> */
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