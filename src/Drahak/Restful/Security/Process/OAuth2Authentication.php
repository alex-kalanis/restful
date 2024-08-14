<?php

namespace Drahak\Restful\Security\Process;

use Drahak\OAuth2;
use Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Drahak\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Drahak\OAuth2\Storage\ITokens;
use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\Exceptions\AuthenticationException;

/**
 * OAuth2Authentication
 * @package Drahak\Restful\Security\Process
 * @author Drahomír Hanák
 */
class OAuth2Authentication extends AuthenticationProcess
{

    public function __construct(
        private readonly AccessTokenFacade $storage,
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
        return $this->storage->getEntity($token);
    }
}
