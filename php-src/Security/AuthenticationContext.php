<?php

namespace kalanis\Restful\Security;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Process\AuthenticationProcess;


/**
 * AuthenticationContext determines which authentication process should use
 * @package kalanis\Restful\Security
 */
class AuthenticationContext
{

    private AuthenticationProcess $process;

    /**
     * Set authentication process to use
     */
    public function setAuthProcess(AuthenticationProcess $process): static
    {
        $this->process = $process;
        return $this;
    }

    /**
     * Authenticate request with authentication process strategy
     * @param IInput $input
     * @throws Exceptions\RequestTimeoutException
     * @throws Exceptions\AuthenticationException
     * @return bool
     */
    public function authenticate(IInput $input): bool
    {
        return $this->process->authenticate($input);
    }
}
