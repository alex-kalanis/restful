<?php

namespace kalanis\Restful\Security\Authentication;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Exceptions\AuthenticationException;
use kalanis\Restful\Security\IAuthTokenCalculator;
use Nette\Http\IRequest;


/**
 * Verify request hashing data and comparing the results
 * @package kalanis\Restful\Security\Authentication
 */
class HashAuthenticator implements IRequestAuthenticator
{

    /** Auth token request header name */
    public const AUTH_HEADER = 'X-HTTP-AUTH-TOKEN';

    public function __construct(
        protected IRequest                             $request,
        protected IAuthTokenCalculator                 $calculator,
    )
    {
    }

    /**
     * @throws AuthenticationException
     */
    public function authenticate(IInput $input): bool
    {
        $requested = $this->getRequestedHash();
        if (!$requested) {
            throw new AuthenticationException('Authentication header not found.');
        }

        $expected = $this->getExpectedHash($input);
        if ($requested !== $expected) {
            throw new AuthenticationException('Authentication tokens do not match.');
        }
        return true;
    }


    /**
     * Get request hash
     * @return string|null
     */
    protected function getRequestedHash(): ?string
    {
        return $this->request->getHeader(self::AUTH_HEADER);
    }

    /**
     * Get expected hash
     * @return string
     */
    protected function getExpectedHash(IInput $input): string
    {
        return $this->calculator->calculate($input);
    }
}
