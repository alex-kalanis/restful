<?php

namespace Drahak\Restful\Security\Process;

use Drahak\OAuth2;
use Drahak\OAuth2\Storage\AccessTokens\AccessTokenFacade;
use Drahak\OAuth2\Storage\InvalidAccessTokenException;
use Drahak\Restful\Http\IInput;
use Drahak\Restful\Security\AuthenticationException;

/**
 * OAuth2Authentication
 * @package Drahak\Restful\Security\Process
 * @author Drahomír Hanák
 */
class OAuth2Authentication extends AuthenticationProcess
{

    public function __construct(private readonly AccessTokenFacade $storage, private readonly OAuth2\Http\IInput $oauthInput)
    {
    }

    /**
     * Authenticate request data
     * @return bool|void
     * @throws AuthenticationException
     */
    protected function authRequestData(IInput $input)
    {
        $token = $this->oauthInput->getAuthorization();
        if (!$token) {
            throw new AuthenticationException('Token was not found.');
        }
    }

    /**
     * Authenticate request timeout
     * @return bool|void
     * @throws AuthenticationException
     */
    protected function authRequestTimeout(IInput $input)
    {
        try {
            $this->getAccessToken();
        } catch (InvalidAccessTokenException $e) {
            throw new AuthenticationException('Invalid or expired access token.', 0, $e);
        }
    }

    /**
     * Get access token
     * @return OAuth2\Storage\AccessTokens\IAccessToken|NULL
     *
     * @throws InvalidAccessTokenException
     */
    public function getAccessToken(): ?\Drahak\OAuth2\Storage\ITokens
    {
        $token = $this->oauthInput->getAuthorization();
        return $this->storage->getEntity($token);
    }


}