<?php

namespace kalanis\Restful\Application;


use kalanis\Restful\Exceptions\InvalidArgumentException;
use Reflector;


/**
 * IAnnotationParser
 * @package kalanis\Restful\Application
 */
interface IAnnotationParser
{

    /**
     * Parse annotation for given class, method or any reflection
     * @param Reflector $reflection
     * @throws InvalidArgumentException
     * @return mixed|void
     *
     */
    public function parse(Reflector $reflection);
}
