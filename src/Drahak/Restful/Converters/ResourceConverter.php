<?php

namespace Drahak\Restful\Converters;

use Nette;

/**
 * ResourceConverter
 * @package Drahak\Restful\Converters
 *
 * @property-read IConverter[] $converters
 */
class ResourceConverter
{
    use Nette\SmartObject;

    /** @var IConverter[] */
    private $converters = [];

    /**
     * Get converters
     * @return IConverter[]
     */
    public function getConverters()
    {
        return $this->converters;
    }

    /**
     * Add resource data converter to list
     */
    public function addConverter(IConverter $converter): static
    {
        $this->converters[] = $converter;
        return $this;
    }

    /**
     * Converts data from resource using converters
     * @return array
     */
    public function convert(array $data)
    {
        /** @var IConverter $converter */
        foreach ($this->converters as $converter) {
            $data = $converter->convert($data);
        }

        return $data;
    }

}
