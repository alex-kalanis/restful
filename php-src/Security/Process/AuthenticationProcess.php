<?php

namespace kalanis\Restful\Security\Process;


use kalanis\Restful\Http\IInput;


/**
 * Request AuthenticationProcess template
 * @package kalanis\Restful\Security\Process
 */
abstract class AuthenticationProcess
{

    /**
     * Authenticate process
     */
    public function authenticate(IInput $input): bool
    {
        $r1 = $this->authRequestData($input);
        $r2 = $this->authRequestTimeout($input);
        return $r1 && $r2;
    }

    /**
     * Authenticate request data
     */
    abstract protected function authRequestData(IInput $input): bool;

    /**
     * Authenticate request timeout
     */
    abstract protected function authRequestTimeout(IInput $input): bool;
}
