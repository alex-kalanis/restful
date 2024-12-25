<?php

namespace Picabo\Restful\Security\Process;

use Nette\Security\User;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Security\Exceptions\AuthenticationException;
use Picabo\Restful\Security\Exceptions\RequestTimeoutException;

/**
 * BasicAuthentication
 * @package Picabo\Restful\Security\Process
 * @author Drahomír Hanák
 */
class BasicAuthentication extends AuthenticationProcess
{

    public function __construct(
        private readonly User $user,
    )
    {
    }

    /**
     * Authenticate request data
     */
    protected function authRequestData(IInput $input): bool
    {
        if (!$this->user->isLoggedIn()) {
            throw new AuthenticationException('User not logged in');
        }
        return true;
    }

    /**
     * Authenticate request timeout
     * @throws RequestTimeoutException
     */
    protected function authRequestTimeout(IInput $input): bool
    {
        if (User::LogoutInactivity === $this->user->getLogoutReason()) {
            throw new RequestTimeoutException('User session expired');
        }
        return true;
    }
}
