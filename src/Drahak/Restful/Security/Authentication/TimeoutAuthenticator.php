<?php

namespace Drahak\Restful\Security\Authentication;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Exceptions\RequestTimeoutException;
use Nette;

/**
 * Verify request timeout to avoid replay attack (needs to be applied with any HashAuthenticator)
 * @package Drahak\Restful\Security\Authentication
 * @author Drahomír Hanák
 */
class TimeoutAuthenticator implements IRequestAuthenticator
{
    use Nette\SmartObject;

    /**
     * @param string $requestTimeKey in user request data
     * @param int $timeout in milliseconds
     */
    public function __construct(
        #[\SensitiveParameter] private readonly string $requestTimeKey,
        private readonly int $timeout,
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

        return TRUE;

    }

}
