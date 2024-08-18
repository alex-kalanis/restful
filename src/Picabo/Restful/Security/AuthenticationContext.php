<?php

namespace Picabo\Restful\Security;

use Nette;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Security\Process\AuthenticationProcess;

/**
 * AuthenticationContext determines which authentication process should use
 * @package Picabo\Restful\Security
 * @author Drahomír Hanák
 */
class AuthenticationContext
{
    use Nette\SmartObject;

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
     * @return bool
     * @throws Exceptions\RequestTimeoutException
     * @throws Exceptions\AuthenticationException
     */
    public function authenticate(IInput $input): bool
    {
        return $this->process->authenticate($input);
    }
}
