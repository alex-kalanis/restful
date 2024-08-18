<?php

namespace Picabo\Restful\Security\Authentication;

use Picabo\Restful\Http\IInput;

/**
 * IRequestAuthenticator
 * @package Picabo\Restful\Security\Authentication
 * @author Drahomír Hanák
 */
interface IRequestAuthenticator
{

    /**
     * Authenticate request
     * @param IInput $input
     * @return bool
     */
    public function authenticate(IInput $input): bool;
}