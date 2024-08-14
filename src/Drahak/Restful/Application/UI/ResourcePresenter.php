<?php

namespace Drahak\Restful\Application\UI;

use Drahak\Restful\Application\Exceptions\BadRequestException;
use Drahak\Restful\Application\IResourcePresenter;
use Drahak\Restful\Application\IResponseFactory;
use Drahak\Restful\Application\Responses\ErrorResponse;
use Drahak\Restful\Http\IInput;
use Drahak\Restful\Http\InputFactory;
use Drahak\Restful\Exceptions\InvalidStateException;
use Drahak\Restful\IResource;
use Drahak\Restful\IResourceFactory;
use Drahak\Restful\Resource\Link;
use Drahak\Restful\Security\AuthenticationContext;
use Drahak\Restful\Security\Exceptions\SecurityException;
use Drahak\Restful\Utils\RequestFilter;
use Drahak\Restful\Validation\IDataProvider;
use Nette\Application;
use Nette\Application\UI;
use Throwable;

/**
 * Base presenter for REST API presenters
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 */
abstract class ResourcePresenter extends UI\Presenter implements IResourcePresenter
{

    /** @internal */
    public const VALIDATE_ACTION_PREFIX = 'validate';

    /** @var IResource */
    protected $resource;

    /** @var RequestFilter */
    protected $requestFilter;

    /** @var IResourceFactory */
    protected $resourceFactory;

    /** @var IResponseFactory */
    protected $responseFactory;

    /** @var AuthenticationContext */
    protected $authentication;

    /** @var IInput|IDataProvider */
    private $input;

    /** InputFactory */
    private $inputFactory;

    /**
     * Inject Drahak Restful
     * @param IInput $input
     */
    public final function injectDrahakRestful(
        IResponseFactory      $responseFactory,
        IResourceFactory $resourceFactory,
        AuthenticationContext $authentication,
        InputFactory $inputFactory,
        RequestFilter $requestFilter
    )
    {
        $this->responseFactory = $responseFactory;
        $this->resourceFactory = $resourceFactory;
        $this->authentication = $authentication;
        $this->requestFilter = $requestFilter;
        $this->inputFactory = $inputFactory;
    }

    /**
     * Check security and other presenter requirements
     */
    public function checkRequirements(\ReflectionClass|\ReflectionMethod $element): void
    {
        try {
            parent::checkRequirements($element);
        } catch (Application\ForbiddenRequestException $e) {
            $this->sendErrorResource($e);
        }

        // Try to authenticate client
        try {
            $this->authentication->authenticate($this->getInput());
        } catch (SecurityException $e) {
            $this->sendErrorResource($e);
        }
    }

    /**
     * Create resource link representation object
     */
    public function link(string $destination, $args = [], $rel = Link::SELF): string
    {
        return new Link(parent::link($destination, $args), $rel);
    }

    /**
     * Presenter startup
     *
     * @throws BadRequestException
     */
    protected function startup(): void
    {
        parent::startup();
        $this->autoCanonicalize = FALSE;

        try {
            // Create resource object
            $this->resource = $this->resourceFactory->create();

            // calls $this->validate<Action>()
            $validationProcessed = $this->tryCall(static::formatValidateMethod($this->getAction()), $this->params);

            // Check if input is validate
            if (!$this->getInput()->isValid() && $validationProcessed === TRUE) {
                $errors = $this->getInput()->validate();
                throw BadRequestException::unprocessableEntity($errors, 'Validation Failed: ' . $errors[0]->message);
            }
        } catch (BadRequestException $e) {
            if ($e->getCode() === 422) {
                $this->sendErrorResource($e);
                return;
            }
            throw $e;
        } catch (InvalidStateException $e) {
            $this->sendErrorResource($e);
        }
    }

    /**
     * Validate action method
     */
    public static function formatValidateMethod(string $action): string
    {
        return self::VALIDATE_ACTION_PREFIX . $action;
    }

    /**
     * Get input
     */
    public function getInput(): IInput
    {
        if (!$this->input) {
            try {
                $this->input = $this->inputFactory->create();
            } catch (BadRequestException $e) {
                $this->sendErrorResource($e);
            }
        }
        return $this->input;
    }

    /**
     * Send error resource to output
     */
    protected function sendErrorResource(Throwable $e, ?string $contentType = NULL): void
    {
        $request = $this->getHttpRequest();

        $this->resource = $this->createErrorResource($e);

        // if the $contentType is not forced and the user has requested an unacceptable content-type, default to JSON
        $accept = $request->getHeader('Accept');
        if ($contentType === NULL && (!$accept || !$this->responseFactory->isAcceptable($accept))) {
            $contentType = IResource::JSON;
        }

        try {
            $this->sendResponse(
                new ErrorResponse(
                    $this->responseFactory->create($this->resource),
                    ($e->getCode() > 99 && $e->getCode() < 600 ? $e->getCode() : 400)
                )
            );
        } catch (InvalidStateException $e) {
            $this->sendErrorResource(BadRequestException::unsupportedMediaType($e->getMessage(), $e), $contentType);
        }
    }

    /**
     * Create error response from exception
     * @param Throwable $e
     * @return IResource
     */
    protected function createErrorResource(Throwable $e): IResource
    {
        $params = [
            'code' => $e->getCode(),
            'status' => 'error',
            'message' => $e->getMessage(),
        ];

        if (isset($e->errors) && $e->errors) {
            $params['errors'] = $e->errors;
        }

        return $this->resourceFactory->create($params);
    }

    /**
     * On before render
     */
    protected function beforeRender()
    {
        parent::beforeRender();
        $this->sendResource();
    }


    /****************** Format methods ******************/

    /**
     * Get REST API response
     * @param string|null $contentType
     * @throws InvalidStateException
     */
    public function sendResource(?string $contentType = NULL): void
    {
        if (!($this->resource instanceof IResource)) {
            $this->resource = $this->resourceFactory->create((array) $this->resource);
        }

        try {
            $response = $this->responseFactory->create($this->resource, $contentType);
            $this->sendResponse($response);
        } catch (InvalidStateException $e) {
            $this->sendErrorResource(BadRequestException::unsupportedMediaType($e->getMessage(), $e), $contentType);
        }
    }
}
