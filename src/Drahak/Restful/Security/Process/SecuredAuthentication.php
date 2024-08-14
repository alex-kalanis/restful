<?php

namespace Drahak\Restful\Security\Process;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Authentication\HashAuthenticator;
use Drahak\Restful\Security\Authentication\TimeoutAuthenticator;

/**
 * SecuredAuthentication process
 * @package Drahak\Restful\Security\Process
 * @author Drahomír Hanák
 */
class SecuredAuthentication extends AuthenticationProcess
{

    public function __construct(
        private readonly HashAuthenticator $hashAuth,
        private readonly TimeoutAuthenticator $timeAuth,
    )
    {
    }

    /**
     * Authenticate request data
     */
    protected function authRequestData(IInput $input): bool
    {
        return $this->hashAuth->authenticate($input);
    }

    /**
     * Authenticate request time
     */
    protected function authRequestTimeout(IInput $input): bool
    {
        return $this->timeAuth->authenticate($input);
    }
}
