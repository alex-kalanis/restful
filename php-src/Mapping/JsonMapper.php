<?php

namespace kalanis\Restful\Mapping;


use kalanis\Restful\Mapping\Exceptions\MappingException;
use Nette\Utils\Json;
use Nette\Utils\JsonException;


/**
 * JsonMapper
 * @package kalanis\Restful\Mapping
 */
class JsonMapper implements IMapper
{

    /**
     * Convert array or Traversable input to string output response
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @throws MappingException
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = true): string
    {
        try {
            return Json::encode($data, $prettyPrint);
        } catch (JsonException $e) {
            throw new MappingException('Error in parsing response: ' . $e->getMessage());
        }
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @throws MappingException
     * @return iterable<string|int, mixed>
     */
    public function parse(mixed $data): iterable
    {
        try {
            return (array) Json::decode(strval($data), true);
        } catch (JsonException $e) {
            throw new MappingException('Error in parsing request: ' . $e->getMessage());
        }
    }
}
