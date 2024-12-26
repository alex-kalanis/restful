<?php

namespace kalanis\Restful\Application;


use kalanis\Restful\Exceptions\InvalidArgumentException;
use kalanis\Restful\Exceptions\InvalidStateException;
use kalanis\Restful\IResource;
use kalanis\Restful\Mapping\MapperContext;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use function str_contains;


/**
 * REST ResponseFactory
 * @package kalanis\Restful\Application
 */
class ResponseFactory implements IResponseFactory
{

    /** @var string|null JSONP request key */
    private ?string $jsonp = null;

    /** @var string pretty print key */
    private string $prettyPrintKey = 'prettyPrint';

    private bool $prettyPrint = true;

    /** @var array<string, class-string> */
    private array $responses = [
        IResource::JSON => Responses\TextResponse::class,
        IResource::JSONP => Responses\JsonpResponse::class,
        IResource::QUERY => Responses\TextResponse::class,
        IResource::XML => Responses\TextResponse::class,
        IResource::FILE => Responses\FileResponse::class,
        IResource::NULL => Responses\NullResponse::class
    ];

    public function __construct(
        private IResponse              $response,
        private readonly IRequest      $request,
        private readonly MapperContext $mapperContext,
    )
    {
    }

    /**
     * Get JSONP key
     * @return string|null [type] [description]
     */
    public function getJsonp(): ?string
    {
        return $this->jsonp;
    }

    /**
     * Set JSONP key
     */
    public function setJsonp(?string $jsonp): self
    {
        $this->jsonp = $jsonp;
        return $this;
    }

    /**
     * Set pretty print key
     */
    public function setPrettyPrintKey(string $prettyPrintKey): self
    {
        $this->prettyPrintKey = $prettyPrintKey;
        return $this;
    }

    /**
     * Register new response type to factory
     * @param string $mimeType
     * @param class-string $responseClass
     * @throws InvalidArgumentException
     * @return $this
     *
     */
    public function registerResponse(string $mimeType, string $responseClass): static
    {
        if (!class_exists($responseClass)) {
            throw new InvalidArgumentException('Response class does not exist.');
        }

        $this->responses[$mimeType] = $responseClass;
        return $this;
    }

    /**
     * Unregister API response from factory
     */
    public function unregisterResponse(string $mimeType): void
    {
        unset($this->responses[$mimeType]);
    }

    /**
     * Set HTTP response
     */
    public function setHttpResponse(IResponse $response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Create new api response
     * @param IResource $resource
     * @param string|null $contentType
     * @throws InvalidStateException
     * @return Responses\IResponse
     */
    public function create(IResource $resource, ?string $contentType = null): Responses\IResponse
    {
        if (is_null($contentType)) {
            $contentType = is_null($this->jsonp) || empty($this->request->getQuery($this->jsonp))
                ? $this->getPreferredContentType(strval($this->request->getHeader('Accept')))
                : IResource::JSONP;
        }

        if (!isset($this->responses[$contentType])) {
            throw new InvalidStateException('Unregistered API response for ' . $contentType);
        }

        if (!class_exists($this->responses[$contentType])) {
            throw new InvalidStateException('API response class does not exist.');
        }

        if (empty($resource->getData())) {
            $this->response->setCode(204); // No content
            $reflection = new \ReflectionClass($this->responses[$contentType]);
            $response = $reflection->newInstance(
                $resource->getData(),
                $this->mapperContext->getMapper($contentType),
            );
            /** @var Responses\IResponse $response */
            return $response;
        }

        $responseClass = $this->responses[$contentType];
        $reflection = new \ReflectionClass($responseClass);
        $response = $reflection->newInstance(
            $resource->getData(),
            $this->mapperContext->getMapper($contentType),
            $contentType,
        );
        /** @var Responses\IResponse $response */
        if ($response instanceof Responses\BaseResponse) {
            $response->setPrettyPrint($this->isPrettyPrint());
        }
        return $response;
    }

    /**
     * Get preferred request content type
     * @param string $contentType may be separated with comma
     * @throws  InvalidStateException If Accept header is unknown
     * @return string
     */
    protected function getPreferredContentType(string $contentType): string
    {
        $accept = explode(',', $contentType);
        $acceptableTypes = array_keys($this->responses);
        if (!$contentType) {
            return $acceptableTypes[0];
        }
        foreach ($accept as $mimeType) {
            if ('*/*' === $mimeType) return $acceptableTypes[0];
            foreach ($acceptableTypes as $formatMime) {
                if (empty($formatMime)) {
                    continue;
                }
                if (str_contains($mimeType, $formatMime)) {
                    return $formatMime;
                }
            }
        }
        throw new InvalidStateException('Unknown Accept header: ' . $contentType);
    }

    /**
     * Is pretty print enabled
     * @return boolean
     */
    protected function isPrettyPrint(): bool
    {
        $prettyPrintKey = $this->request->getQuery($this->prettyPrintKey);
        if ('false' === $prettyPrintKey) {
            return false;
        }
        if ('true' === $prettyPrintKey) {
            return true;
        }
        return $this->prettyPrint;
    }

    /**
     * Set pretty print
     */
    public function setPrettyPrint(bool $prettyPrint): self
    {
        $this->prettyPrint = $prettyPrint;
        return $this;
    }

    /**
     * Is given content type acceptable for response
     */
    public function isAcceptable(string $contentType): bool
    {
        try {
            $this->getPreferredContentType($contentType);
            return true;
        } catch (InvalidStateException) {
            return false;
        }
    }
}
