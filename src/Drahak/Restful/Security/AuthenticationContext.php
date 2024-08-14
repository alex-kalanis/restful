<?php

namespace Drahak\Restful\Security;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Process\AuthenticationProcess;
use Nette;

/**
 * AuthenticationContext determines which authentication process should use
 * @package Drahak\Restful\Security
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
     * @throws Exceptions\AuthenticationException
     * @throws Exceptions\RequestTimeoutException
     * @return bool
     */
    public function authenticate(IInput $input): bool
    {
        return $this->process->authenticate($input);
    }
}
