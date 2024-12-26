<?php

namespace kalanis\Restful\Converters;


/**
 * ResourceConverter
 * @package kalanis\Restful\Converters
 */
class ResourceConverter
{

    /** @var array<IConverter<string|int, mixed>> */
    private array $converters = [];

    /**
     * Get converters
     * @return array<IConverter<string|int, mixed>>
     */
    public function getConverters(): array
    {
        return $this->converters;
    }

    /**
     * Add resource data converter to list
     * @param IConverter<string|int, mixed> $converter
     * @return $this
     */
    public function addConverter(IConverter $converter): self
    {
        $this->converters[] = $converter;
        return $this;
    }

    /**
     * Converts data from resource using converters
     * @param array<string|int, mixed> $data
     * @return array<string|int, mixed>
     */
    public function convert(array $data): array
    {
        foreach ($this->converters as $converter) {
            $data = $converter->convert($data);
        }

        return $data;
    }
}
