<?php

namespace Drahak\Restful\Mapping;

use Drahak\Restful\Exceptions\InvalidArgumentException;
use Drahak\Restful\Mapping\Exceptions\MappingException;
use Drahak\Restful\Resource\Media;
use Nette;
use Nette\Utils\Strings;

/**
 * DataUrlMapper - encode or decode base64 file
 * @package Drahak\Restful\Mapping
 * @author Drahomír Hanák
 */
class DataUrlMapper implements IMapper
{
    use Nette\SmartObject;

    /**
     * Create DATA URL from file
     * @param Media $data
     * @param bool $prettyPrint
     * @return string
     *
     * @throws InvalidArgumentException
     */
    public function stringify(iterable|string $data, bool $prettyPrint = TRUE): string
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
     * @param string $data
     * @throws MappingException
     * @return iterable|string
     */
    public function parse(mixed $data): iterable|string
    {
        $matches = Strings::match(strval($data), "@^data:([\w/]+?);(\w+?),(.*)$@si");
        if (!$matches) {
            throw new MappingException('Given data URL is invalid.');
        }

        return new Media(base64_decode((string) $matches[3]), $matches[1]);
    }

}
