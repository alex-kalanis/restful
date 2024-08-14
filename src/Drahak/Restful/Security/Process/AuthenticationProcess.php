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
     * @return bool
     */
    public function authenticate(IInput $input)
    {
        $this->authRequestData($input);
        $this->authRequestTimeout($input);
        return TRUE;
    }

    /**
     * Authenticate request data
     * @return bool
     */
    abstract protected function authRequestData(IInput $input);

    /**
     * Authenticate request timeout
     * @return bool
     */
    abstract protected function authRequestTimeout(IInput $input);

}
