<?php

namespace kalanis\Restful\Application\Responses;


use kalanis\Restful\Mapping\IMapper;
use stdClass;


/**
 * BaseResponse
 * @package kalanis\Restful\Application\Responses
 */
abstract class BaseResponse implements IResponse
{

    /**
     * @var iterable<string|int, mixed>|stdClass
     */
    protected iterable|stdClass $data = [];

    private bool $prettyPrint = true;

    public function __construct(
        protected IMapper              $mapper,
        protected readonly string|null $contentType = null,
    )
    {
    }

    /**
     * Is pretty print enabled
     */
    public function isPrettyPrint(): bool
    {
        return $this->prettyPrint;
    }

    /**
     * Set pretty print
     */
    public function setPrettyPrint(bool $pretty): self
    {
        $this->prettyPrint = $pretty;
        return $this;
    }

    /**
     * Get response content type
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Get response data
     * @return iterable<string|int, mixed>|stdClass|string
     */
    public function getData(): iterable|stdClass|string
    {
        return $this->data;
    }

    /**
     * Set mapper
     */
    public function setMapper(IMapper $mapper): self
    {
        $this->mapper = $mapper;
        return $this;
    }
}
