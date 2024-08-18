<?php

namespace Picabo\Restful\Application;

use Picabo\Restful\Exceptions\InvalidArgumentException;
use Reflector;

/**
 * IAnnotationParser
 * @package Picabo\Restful
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
