<?php

namespace Drahak\Restful\Converters;

use Nette;

/**
 * ResourceConverter
 * @package Drahak\Restful\Converters
 */
class ResourceConverter
{
    use Nette\SmartObject;

    /** @var IConverter[] */
    private array $converters = [];

    /**
     * Get converters
     * @return IConverter[]
     */
    public function getConverters(): array
    {
        return $this->converters;
    }

    /**
     * Add resource data converter to list
     */
    public function addConverter(IConverter $converter): self
    {
        $this->converters[] = $converter;
        return $this;
    }

    /**
     * Converts data from resource using converters
     */
    public function convert(array $data): array
    {
        foreach ($this->converters as $converter) {
            $data = $converter->convert($data);
        }

        return $data;
    }
}
