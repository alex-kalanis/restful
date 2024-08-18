<?php

namespace Picabo\Restful\Mapping;

use Nette;
use Nette\Utils\Strings;
use Picabo\Restful\Exceptions\InvalidStateException;

/**
 * MapperContext
 * @package Picabo\Restful\Mapping
 * @author Drahomír Hanák
 */
class MapperContext
{
    use Nette\SmartObject;

    /** @var array<string, IMapper> */
    protected array $services = [];

    /**
     * Add mapper
     */
    public function addMapper(string $contentType, IMapper $mapper): void
    {
        $this->services[$contentType] = $mapper;
    }

    /**
     * Get mapper
     * @param string $contentType in format mimeType[; charset=utf8]
     * @return IMapper
     *
     * @throws InvalidStateException
     */
    public function getMapper(string $contentType): IMapper
    {
        $contentType = explode(';', $contentType);
        $contentType = Strings::trim($contentType[0]);
        $contentType = $contentType ?: 'NULL';
        if (!isset($this->services[$contentType])) {
            throw new InvalidStateException('There is no mapper for Content-Type: ' . $contentType);
        }
        return $this->services[$contentType];
    }
}
