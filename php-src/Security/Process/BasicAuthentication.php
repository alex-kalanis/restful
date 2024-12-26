<?php

namespace kalanis\Restful\Security\Process;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Exceptions\AuthenticationException;
use kalanis\Restful\Security\Exceptions\RequestTimeoutException;
use Nette\Security\User;


/**
 * BasicAuthentication
 * @package kalanis\Restful\Security\Process
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
