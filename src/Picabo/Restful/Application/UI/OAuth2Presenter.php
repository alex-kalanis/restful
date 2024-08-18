<?php

namespace Picabo\Restful\Application\UI;

use Nette\Http\Url;
use Picabo\OAuth2\Application\IOAuthPresenter;
use Picabo\OAuth2\Exceptions\InvalidGrantException;
use Picabo\OAuth2\Exceptions\InvalidStateException;
use Picabo\OAuth2\Exceptions\OAuthException;
use Picabo\OAuth2\Exceptions\UnauthorizedClientException;
use Picabo\OAuth2\Exceptions\UnsupportedResponseTypeException;
use Picabo\OAuth2\Grant\GrantContext;
use Picabo\OAuth2\Grant\GrantType;
use Picabo\OAuth2\Grant\IGrant;
use Picabo\OAuth2\Storage\AuthorizationCodes\AuthorizationCodeFacade;
use Picabo\OAuth2\Storage\Clients\IClient;
use Picabo\OAuth2\Storage\Clients\IClientStorage;
use Picabo\OAuth2\Storage\Exceptions\TokenException;
use Traversable;

/**
 * OAuth2Presenter
 * @package Picabo\Restful\Application
 * @author Drahomír Hanák
 */
class OAuth2Presenter extends ResourcePresenter implements IOAuthPresenter
{

    /** @var AuthorizationCodeFacade */
    protected $authorizationCode;
    /** @var IClientStorage */
    protected $clientStorage;
    /** @var IClient */
    protected $client;
    /** @var GrantContext */
    private $grantContext;

    /**
     * Inject OAuth2 presenter dependencies
     */
    public final function injectOauth2(
        GrantContext            $grantContext,
        AuthorizationCodeFacade $authorizationCode,
        IClientStorage          $clientStorage)
    {
        $this->grantContext = $grantContext;
        $this->clientStorage = $clientStorage;
        $this->authorizationCode = $authorizationCode;
    }

    /**
     * Issue an authorization code
     * @param string $responseType
     * @param string $redirectUrl
     * @param string|null $scope
     * @return void
     *
     * @throws UnauthorizedClientException
     * @throws UnsupportedResponseTypeException
     */
    public function issueAuthorizationCode(string $responseType, string $redirectUrl, ?string $scope = NULL): void
    {
        try {
            if ($responseType !== 'code') {
                throw new UnsupportedResponseTypeException;
            }
            if (!$this->client->getId()) {
                throw new UnauthorizedClientException;
            }

            $scope = array_filter(explode(',', str_replace(' ', ',', $scope)));
            $code = $this->authorizationCode->create($this->client, $this->user->getId(), $scope);
            $data = ['code' => $code->getAuthorizationCode()];
            $this->oauthResponse($data, $redirectUrl);
        } catch (OAuthException $e) {
            $this->oauthError($e);
        } catch (TokenException) {
            $this->oauthError(new InvalidGrantException());
        }
    }

    /**
     * Send OAuth response
     * @param array|Traversable $data
     * @param string|null $redirectUrl
     * @param int $code
     */
    public function oauthResponse(iterable $data, ?string $redirectUrl = NULL, int $code = 200): void
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        $data = (array)$data;

        // Redirect, if there is URL
        if ($redirectUrl !== NULL) {
            $url = new Url($redirectUrl);
            if ($this->getParameter('response_type') == 'token') {
                $url->setFragment(http_build_query($data));
            } else {
                $url->appendQuery($data);
            }
            $this->redirectUrl($url);
        }

        // else send JSON response
        foreach ($data as $key => $value) {
            $this->resource->$key = $value;
        }
        $this->sendResource(NULL);
    }

    /**
     * Provide OAuth2 error response (redirect or at least JSON)
     * @param OAuthException $exception
     */
    public function oauthError(OAuthException $exception): void
    {
        $error = ['error' => $exception->getKey(), 'error_description' => $exception->getMessage()];
        $this->oauthResponse($error, $this->getParameter('redirect_uri'), $exception->getCode());
    }

    /**
     * Issue an access token
     * @param string|null $grantType
     * @param string|null $redirectUrl
     */
    public function issueAccessToken(?string $grantType = NULL, ?string $redirectUrl = NULL): void
    {
        try {
            if ($grantType !== NULL) {
                $grantType = $this->grantContext->getGrantType($grantType);
            } else {
                $grantType = $this->getGrantType();
            }

            $response = $grantType->getAccessToken($this->getHttpRequest());
            $this->oauthResponse($response, $redirectUrl);
        } catch (OAuthException $e) {
            $this->oauthError($e);
        } catch (TokenException) {
            $this->oauthError(new InvalidGrantException);
        }
    }


    /**************** OAuth2 presenter methods ****************/
    /**
     * Get grant type
     * @throws UnsupportedResponseTypeException
     */
    public function getGrantType(): IGrant
    {
        $request = $this->getHttpRequest();
        $grantType = $request->getPost(GrantType::GRANT_TYPE_KEY);
        try {
            return $this->grantContext->getGrantType($grantType);
        } catch (InvalidStateException $e) {
            throw new UnsupportedResponseTypeException('Trying to use unknown grant type ' . $grantType, $e);
        }
    }

    /**
     * On presenter startup
     */
    protected function startup(): void
    {
        parent::startup();
        $this->client = $this->clientStorage->getClient(
            $this->getParameter(GrantType::CLIENT_ID_KEY),
            $this->getParameter(GrantType::CLIENT_SECRET_KEY)
        );
    }
}
