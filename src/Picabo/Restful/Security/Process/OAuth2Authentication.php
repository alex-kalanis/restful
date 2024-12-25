<?php

namespace Picabo\Restful\Security\Process;

use Picabo\OAuth2;
use Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Picabo\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Picabo\OAuth2\Storage\ITokens;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Security\Exceptions\AuthenticationException;

/**
 * OAuth2Authentication
 * @package Picabo\Restful\Security\Process
 * @author Drahomír Hanák
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
     * @return ITokens|NULL
     */
    public function getAccessToken(): ?ITokens
    {
        $token = $this->oauthInput->getAuthorization();
        return $token ? $this->storage->getEntity($token) : null;
    }
}
