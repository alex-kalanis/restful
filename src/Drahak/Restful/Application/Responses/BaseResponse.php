<?php

namespace Drahak\Restful\Application\Responses;

use Drahak;
use Drahak\Restful\Mapping\IMapper;
use Nette;
use stdClass;

/**
 * BaseResponse
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
abstract class BaseResponse implements IResponse
{
    use Nette\SmartObject;

    protected iterable|stdClass $data = [];

    private bool $prettyPrint = TRUE;

    public function __construct(
        protected IMapper $mapper,
        protected readonly string|null $contentType = NULL,
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
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Get response data
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
