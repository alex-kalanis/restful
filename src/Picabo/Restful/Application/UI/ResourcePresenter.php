<?php

namespace Picabo\Restful\Application\UI;

use Nette\Application;
use Nette\Application\UI;
use Picabo\Restful\Application\Exceptions\BadRequestException;
use Picabo\Restful\Application\IResourcePresenter;
use Picabo\Restful\Application\IResponseFactory;
use Picabo\Restful\Application\Responses\ErrorResponse;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Http\InputFactory;
use Picabo\Restful\IResource;
use Picabo\Restful\IResourceFactory;
use Picabo\Restful\Resource\Link;
use Picabo\Restful\Security\AuthenticationContext;
use Picabo\Restful\Security\Exceptions\SecurityException;
use Picabo\Restful\Utils\RequestFilter;
use Picabo\Restful\Validation\IDataProvider;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

/**
 * Base presenter for REST API presenters
 * @package Picabo\Restful\Application
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
     * Inject Picabo Restful
     * @param IInput $input
     */
    public final function injectPicaboRestful(
        IResponseFactory      $responseFactory,
        IResourceFactory      $resourceFactory,
        AuthenticationContext $authentication,
        InputFactory          $inputFactory,
        RequestFilter         $requestFilter
    ): void
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
    public function checkRequirements(ReflectionClass|ReflectionMethod $element): void
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
            $this->resource = $this->resourceFactory->create((array)$this->resource);
        }

        try {
            $response = $this->responseFactory->create($this->resource, $contentType);
            $this->sendResponse($response);
        } catch (InvalidStateException $e) {
            $this->sendErrorResource(BadRequestException::unsupportedMediaType($e->getMessage(), $e), $contentType);
        }
    }
}
