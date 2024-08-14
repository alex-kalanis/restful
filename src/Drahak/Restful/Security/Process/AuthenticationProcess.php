<?php

namespace Drahak\Restful\Security\Process;

use Drahak\Restful\Http\IInput;
use Nette;

/**
 * Request AuthenticationProcess template
 * @package Drahak\Restful\Security\Process
 * @author Drahomír Hanák
 */
abstract class AuthenticationProcess
{
    use Nette\SmartObject;

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
