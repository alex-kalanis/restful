<?php

namespace kalanis\Restful;


use kalanis\Restful\Converters\ResourceConverter;


/**
 * ConvertedResource
 * @package kalanis\Restful
 * @template TK of string
 * @template TVal of mixed
 * @extends Resource<TK, TVal>
 */
class ConvertedResource extends Resource
{
    /**
     * @param ResourceConverter $resourceConverter
     * @param array<string, mixed> $data
     */
    public function __construct(
        private readonly ResourceConverter $resourceConverter,
        array                              $data = [],
    )
    {
        parent::__construct($data);
    }

    /**
     * Get parsed resource
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->resourceConverter->convert(parent::getData());
    }
}
