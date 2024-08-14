<?php

namespace Drahak\Restful\Converters;

use Drahak\Restful\IResource;
use Nette;
use stdClass;
use Traversable;

/**
 * ObjectConverter
 * @package Drahak\Restful\Converters
 * @author Drahomír Hanák
 */
class ObjectConverter implements IConverter
{
    use Nette\SmartObject;

    /**
     * Converts stdClass and traversable objects in resource to array
     */
    public function convert(array $resource): array
    {
        return (array) $this->parseObjects($resource);
    }

    /**
     * Parse objects in resource
     * @param array|Traversable|stdClass $data
     * @return array
     */
    protected function parseObjects($data)
    {
        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        } else if ($data instanceof stdClass) {
            $data = (array) $data;
        }

        foreach ($data as $key => $value) {
            if ($value instanceof IResource) {
                $value = $value->getData();
            }

            if ($value instanceof Traversable || $value instanceof stdClass || is_array($value)) {
                $data[$key] = $this->parseObjects($value);
            }
        }
        return $data;
    }

}
