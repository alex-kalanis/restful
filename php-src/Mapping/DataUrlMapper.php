<?php

namespace kalanis\Restful\Mapping;


use kalanis\Restful\Exceptions\InvalidArgumentException;
use kalanis\Restful\Mapping\Exceptions\MappingException;
use kalanis\Restful\Resource\Media;
use Nette\Utils\Strings;


/**
 * DataUrlMapper - encode or decode base64 file
 * @package kalanis\Restful\Mapping
 */
class DataUrlMapper implements IMapper
{

    /**
     * Create DATA URL from file
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @throws InvalidArgumentException
     * @return string
     *
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = true): string
    {
        if (!$data instanceof Media) {
            throw new InvalidArgumentException(
                'DataUrlMapper expects object of type Media, ' . (gettype($data)) . ' given'
            );
        }
        return sprintf('data:%s;base64,%s', $data->getContentType(), base64_encode($data->getContent()));
    }

    /**
     * Convert client request data to array or traversable
     * @param mixed $data
     * @throws MappingException
     * @return object
     */
    public function parse(mixed $data): object
    {
        $matches = Strings::match(strval($data), "@^data:([\w/]+?);(\w+?),(.*)$@si");
        if (!$matches) {
            throw new MappingException('Given data URL is invalid.');
        }

        return new Media(base64_decode((string) $matches[3]), $matches[1]);
    }
}
