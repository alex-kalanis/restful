<?php

namespace kalanis\Restful\Security\Process;


use kalanis\Restful\Http\IInput;
use kalanis\Restful\Security\Exceptions\AuthenticationException;
use kalanis\OAuth2;
use kalanis\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use kalanis\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use kalanis\OAuth2\Storage\ITokens;


/**
 * OAuth2Authentication
 * @package kalanis\Restful\Security\Process
 */
class OAuth2Authentication extends AuthenticationProcess
{

    public function __construct(
        private readonly AccessTokenFacade  $storage,
        private readonly OAuth2\Http\IInput $oauthInput,
    )
    {
    }

    /**
     * Authenticate request data
     * @param IInput $input
     * @throws AuthenticationException
     * @return bool
     */
    protected function authRequestData(IInput $input): bool
    {
        $token = $this->oauthInput->getAuthorization();
        if (!$token) {
            throw new AuthenticationException('Token was not found.');
        }
        return true;
    }

    /**
     * Authenticate request timeout
     * @param IInput $input
     * @throws AuthenticationException
     * @return bool
     */
    protected function authRequestTimeout(IInput $input): bool
    {
        try {
            $this->getAccessToken();
        } catch (InvalidAccessTokenException $e) {
            throw new AuthenticationException('Invalid or expired access token.', 0, $e);
        }
        return true;
    }

    /**
     * Get access token
     * @throws InvalidAccessTokenException
     * @return ITokens|null
     */
    public function getAccessToken(): ?ITokens
    {
        $token = $this->oauthInput->getAuthorization();
        return $token ? $this->storage->getEntity($token) : null;
    }
}
