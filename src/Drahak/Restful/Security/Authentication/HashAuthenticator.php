<?php

namespace Drahak\Restful\Security\Authentication;

use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\AuthenticationException;
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
    /** @var IRequest */
    protected $request;
    /** @var IAuthTokenCalculator */
    protected $calculator;

    /**
     * @param string $privateKey
     */
    public function __construct(private $privateKey, IRequest $request, IAuthTokenCalculator $calculator)
    {
        $this->request = $request;
        $this->calculator = $calculator;
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
     * @return string
     */
    protected function getRequestedHash(): ?string
    {
        return $this->request->getHeader(self::AUTH_HEADER);
    }

    /**
     * Get expected hash
     * @return string
     */
    protected function getExpectedHash(IInput $input)
    {
        return $this->calculator->calculate($input);
    }

}
