<?php

namespace kalanis\Restful\Application\UI;


use Nette\Http\Url;
use kalanis\OAuth2\Application\IOAuthPresenter;
use kalanis\OAuth2\Exceptions\InvalidGrantException;
use kalanis\OAuth2\Exceptions\InvalidStateException;
use kalanis\OAuth2\Exceptions\OAuthException;
use kalanis\OAuth2\Exceptions\UnauthorizedClientException;
use kalanis\OAuth2\Exceptions\UnsupportedResponseTypeException;
use kalanis\OAuth2\Grant\GrantContext;
use kalanis\OAuth2\Grant\GrantType;
use kalanis\OAuth2\Grant\IGrant;
use kalanis\OAuth2\Storage\AuthorizationCodes\AuthorizationCodeFacade;
use kalanis\OAuth2\Storage\Clients\IClient;
use kalanis\OAuth2\Storage\Clients\IClientStorage;
use kalanis\OAuth2\Storage\Exceptions\TokenException;
use Traversable;


/**
 * OAuth2Presenter
 * @package kalanis\Restful\Application
 */
class OAuth2Presenter extends ResourcePresenter implements IOAuthPresenter
{

    #[\Nette\DI\Attributes\Inject]
    protected AuthorizationCodeFacade $authorizationCode;

    #[\Nette\DI\Attributes\Inject]
    public IClientStorage $clientStorage;

    #[\Nette\DI\Attributes\Inject]
    public GrantContext $grantContext;

    protected IClient $client;

    /**
     * Issue an authorization code
     * @param string $responseType
     * @param string $redirectUrl
     * @param string|null $scope
     * @throws UnauthorizedClientException
     * @throws UnsupportedResponseTypeException
     * @return void
     *
     */
    public function issueAuthorizationCode(string $responseType, string $redirectUrl, ?string $scope = null): void
    {
        try {
            if ('code' !== $responseType) {
                throw new UnsupportedResponseTypeException;
            }
            if (!$this->client->getId()) {
                throw new UnauthorizedClientException;
            }

            $scope = array_filter(explode(',', str_replace(' ', ',', strval($scope))));
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
     * @param array<string, mixed>|Traversable<string, mixed> $data
     * @param string|null $redirectUrl
     * @param int $code
     */
    public function oauthResponse(iterable $data, ?string $redirectUrl = null, int $code = 200): void
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }
        $data = (array) $data;

        // Redirect, if there is URL
        if (null !== $redirectUrl) {
            $url = new Url($redirectUrl);
            if ('token' == $this->getParameter('response_type')) {
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
        $this->sendResource(null);
    }

    /**
     * Provide OAuth2 error response (redirect or at least JSON)
     * @param OAuthException $exception
     */
    public function oauthError(OAuthException $exception): void
    {
        $error = ['error' => $exception->getKey(), 'error_description' => $exception->getMessage()];
        $redirect = $this->getParameter('redirect_uri');
        $this->oauthResponse($error, is_null($redirect) ? null : strval($redirect), $exception->getCode());
    }

    /**
     * Issue an access token
     * @param string|null $grantType
     * @param string|null $redirectUrl
     */
    public function issueAccessToken(?string $grantType = null, ?string $redirectUrl = null): void
    {
        try {
            if (null !== $grantType) {
                $grantType = $this->grantContext->getGrantType($grantType);
            } else {
                $grantType = $this->getGrantType();
            }

            $response = $grantType->getAccessToken();
            $this->oauthResponse($response, $redirectUrl);
        } catch (OAuthException $e) {
            $this->oauthError($e);
        } catch (TokenException) {
            $this->oauthError(new InvalidGrantException);
        }
    }

    /**
     * Get grant type
     * @throws UnsupportedResponseTypeException
     */
    public function getGrantType(): IGrant
    {
        $request = $this->getHttpRequest();
        $grantType = strval($request->getPost(GrantType::GRANT_TYPE_KEY));
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
        $clientId = $this->getParameter(GrantType::CLIENT_ID_KEY);
        $clientSecret = $this->getParameter(GrantType::CLIENT_SECRET_KEY);
        $client = $this->clientStorage->getClient(
            is_numeric($clientId) ? intval($clientId) : strval($clientId),
            is_null($clientSecret) ? null : strval($clientSecret)
        );
        if (!$client) {
            throw new \LogicException('Cannot load client info');
        }
        $this->client = $client;
    }
}
