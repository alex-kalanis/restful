<?php

namespace kalanis\Restful\Security\Process;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Authentication\HashAuthenticator;
use kalanis\Restful\Security\Authentication\TimeoutAuthenticator;


/**
 * SecuredAuthentication process
 * @package kalanis\Restful\Security\Process
 */
class SecuredAuthentication extends AuthenticationProcess
{

    public function __construct(
        private readonly HashAuthenticator    $hashAuth,
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
