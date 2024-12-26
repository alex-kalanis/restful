<?php

namespace kalanis\Restful\Mapping;


use kalanis\Restful\Exceptions\InvalidStateException;
use Nette\Utils\Strings;


/**
 * MapperContext
 * @package kalanis\Restful\Mapping
 */
class MapperContext
{

    /** @var array<string, IMapper> */
    protected array $services = [];

    /**
     * Add mapper
     */
    public function addMapper(?string $contentType, IMapper $mapper): void
    {
        $this->services[$this->safeContentType($contentType)] = $mapper;
    }

    /**
     * Get mapper
     * @param string $contentType in format mimeType[; charset=utf8]
     * @throws InvalidStateException
     * @return IMapper
     *
     */
    public function getMapper(?string $contentType): IMapper
    {
        $contentType = explode(';', strval($contentType));
        $contentType = $this->safeContentType($contentType[0] ?: null);
        if (!isset($this->services[$contentType])) {
            throw new InvalidStateException('There is no mapper for Content-Type: ' . $contentType);
        }
        return $this->services[$contentType];
    }

    protected function safeContentType(?string $contentType): string
    {
        if (is_null($contentType)) {
            return 'NULL';
        }
        $contentType = Strings::trim($contentType);
        return $contentType ?: 'NULL';
    }
}
