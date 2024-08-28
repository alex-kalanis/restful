<?php

namespace Picabo\Restful\Mapping;

use Nette;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Picabo\Restful\Mapping\Exceptions\MappingException;

/**
 * JsonMapper
 * @package Picabo\Restful\Mapping
 * @author Drahomír Hanák
 */
class JsonMapper implements IMapper
{
    use Nette\SmartObject;

    /**
     * Convert array or Traversable input to string output response
     * @param iterable|string $data
     * @param bool $prettyPrint
     * @return string
     * @throws MappingException
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = TRUE): string
    {
        try {
            return Json::encode($data, $prettyPrint);
        } catch (JsonException $e) {
            throw new MappingException('Error in parsing response: ' . $e->getMessage());
        }
    }

    /**
     * Convert client request data to array or traversable
     * @param string $data
     * @return iterable|string
     *
     * @throws MappingException
     */
    public function parse(mixed $data): iterable|string|object
    {
        try {
            return Json::decode($data, true);
        } catch (JsonException $e) {
            throw new MappingException('Error in parsing request: ' . $e->getMessage());
        }
    }

}
