<?php

namespace kalanis\Restful\Security\Authentication;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Exceptions\RequestTimeoutException;


/**
 * Verify request timeout to avoid replay attack (needs to be applied with any HashAuthenticator)
 * @package kalanis\Restful\Security\Authentication
 */
class TimeoutAuthenticator implements IRequestAuthenticator
{

    /**
     * @param string $requestTimeKey in user request data
     * @param int $timeout in milliseconds
     */
    public function __construct(
        #[\SensitiveParameter] private readonly string $requestTimeKey,
        private readonly int                           $timeout,
    )
    {
    }

    /**
     * Authenticate request timeout
     *
     * @throws RequestTimeoutException
     */
    public function authenticate(IInput $input): bool
    {
        $timestamp = time();
        $data = $input->getData();
        if (!isset($data[$this->requestTimeKey]) || !$data[$this->requestTimeKey]) {
            throw new RequestTimeoutException('Request time not found in requested data.');
        }

        $diff = $timestamp - $data[$this->requestTimeKey];
        if ($diff > $this->timeout) {
            throw new RequestTimeoutException('Request timeout');
        }

        return true;
    }
}
