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

    /** @var AuthenticationProcess */
    private $process;

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
     * @return bool
     *
     * @throws AuthenticationException
     * @throws RequestTimeoutException
     */
    public function authenticate(IInput $input)
    {
        return $this->process->authenticate($input);
    }

}