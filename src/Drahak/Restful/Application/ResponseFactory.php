<?php

namespace Drahak\Restful\Application;

use Drahak\Restful\Exceptions\InvalidArgumentException;
use Drahak\Restful\Exceptions\InvalidStateException;
use Drahak\Restful\IResource;
use Drahak\Restful\Mapping\MapperContext;
use Nette;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

/**
 * REST ResponseFactory
 * @package Drahak\Restful
 * @author Drahomír Hanák
 */
class ResponseFactory implements IResponseFactory
{
    use Nette\SmartObject;

    /** @var string JSONP request key */
    private string $jsonp = '';

    /** @var string pretty print key */
    private string $prettyPrintKey = 'prettyPrint';

    private bool $prettyPrint = TRUE;

    /** @var array */
    private array $responses = [
        IResource::JSON => Responses\TextResponse::class,
        IResource::JSONP => Responses\JsonpResponse::class,
        IResource::QUERY => Responses\TextResponse::class,
        IResource::XML => Responses\TextResponse::class,
        IResource::FILE => Responses\FileResponse::class,
        IResource::NULL => Responses\NullResponse::class
    ];

    public function __construct(
        private IResponse $response,
        private readonly IRequest $request,
        private readonly MapperContext $mapperContext
    )
    {
    }

    /**
     * Get JSONP key
     * @return [type] [description]
     */
    public function getJsonp(): string
    {
        return $this->jsonp;
    }

    /**
     * Set JSONP key
     */
    public function setJsonp(string $jsonp): self
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
     * @param string $responseClass
     * @return $this
     *
     * @throws InvalidArgumentException
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
     * @return Responses\IResponse
     * @throws InvalidStateException
     */
    public function create(IResource $resource, ?string $contentType = NULL): Responses\IResponse
    {
        if ($contentType === NULL) {
            $contentType = $this->jsonp === FALSE || !$this->request->getQuery($this->jsonp) ?
                $this->getPreferredContentType($this->request->getHeader('Accept')) :
                IResource::JSONP;
        }

        if (!isset($this->responses[$contentType])) {
            throw new InvalidStateException('Unregistered API response for ' . $contentType);
        }

        if (!class_exists($this->responses[$contentType])) {
            throw new InvalidStateException('API response class does not exist.');
        }

        if (empty($resource->getData())) {
            $this->response->setCode(204); // No content
            return new $this->responses[IResource::NULL];
        }

        $responseClass = $this->responses[$contentType];
        $response = new $responseClass($resource->getData(), $this->mapperContext->getMapper($contentType), $contentType);
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
            if ($mimeType === '*/*') return $acceptableTypes[0];
            foreach ($acceptableTypes as $formatMime) {
                if (empty($formatMime)) {
                    continue;
                }
                if (\str_contains($mimeType, $formatMime)) {
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
        if ($prettyPrintKey === 'false') {
            return FALSE;
        }
        if ($prettyPrintKey === 'true') {
            return TRUE;
        }
        return $this->prettyPrint;
    }

    /**
     * Set pretty print
     */
    public function setPrettyPrint(string $prettyPrint): self
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
            return TRUE;
        } catch (InvalidStateException) {
            return FALSE;
        }
    }
}
