<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\Exceptions\InvalidArgumentException;
use Reflector;

/**
 * IAnnotationParser
 * @package Drahak\Restful
 * @author Drahomír Hanák
 */
interface IAnnotationParser
{

    /**
     * Parse annotation for given class, method or any reflection
     * @param Reflector $reflection
     * @return mixed|void
     *
     * @throws InvalidArgumentException
     */
    public function parse(Reflector $reflection);
}
