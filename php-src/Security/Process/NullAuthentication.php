<?php

namespace kalanis\Restful\Security\Process;


use kalanis\Restful\Http\IInput;


/**
 * NullAuthentication for non-secured API requests
 * @package kalanis\Restful\Security\Process
 */
class NullAuthentication extends AuthenticationProcess
{
    /**
     * Authenticate request data
     */
    protected function authRequestData(IInput $input): bool
    {
        return true;
    }

    /**
     * Authenticate request time
     */
    protected function authRequestTimeout(IInput $input): bool
    {
        return true;
    }
}
