<?php

namespace Drahak\Restful\Security\Process;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Exceptions\AuthenticationException;
use Drahak\Restful\Security\Exceptions\RequestTimeoutException;
use Nette\Security\User;

/**
 * BasicAuthentication
 * @package Drahak\Restful\Security\Process
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
        if ($this->user->getLogoutReason() === User::LogoutInactivity) {
            throw new RequestTimeoutException('User session expired');
        }
        return true;
    }
}
