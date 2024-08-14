<?php

namespace Drahak\Restful\Security\Authentication;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Exceptions\AuthenticationException;
use Drahak\Restful\Security\IAuthTokenCalculator;
use Nette;
use Nette\Http\IRequest;

/**
 * Verify request hashing data and comparing the results
 * @package Drahak\Restful\Security\Authentication
 * @author Drahomír Hanák
 */
class HashAuthenticator implements IRequestAuthenticator
{
    use Nette\SmartObject;

    /** Auth token request header name */
    public const AUTH_HEADER = 'X-HTTP-AUTH-TOKEN';

    public function __construct(
        #[\SensitiveParameter] private readonly string $privateKey,
        protected IRequest $request,
        protected IAuthTokenCalculator $calculator,
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
        return TRUE;
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
