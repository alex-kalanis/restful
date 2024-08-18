<?php

namespace Picabo\Restful\Security\Process;

use Picabo\Restful\Http\IInput;

/**
 * NullAuthentication for non-secured API requests
 * @package Picabo\Restful\Security\Process
 * @author Drahomír Hanák
 */
class NullAuthentication extends AuthenticationProcess
{
    /**
     * Authenticate request data
     */
    protected function authRequestData(IInput $input): bool
    {
        return TRUE;
    }

    /**
     * Authenticate request time
     */
    protected function authRequestTimeout(IInput $input): bool
    {
        return TRUE;
    }
}
