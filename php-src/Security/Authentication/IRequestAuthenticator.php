<?php

namespace kalanis\Restful\Security\Authentication;


use kalanis\Restful\Http\IInput;


/**
 * IRequestAuthenticator
 * @package kalanis\Restful\Security\Authentication
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
