<?php

namespace Drahak\Restful\Application\Responses;

use Drahak\Restful\Mapping\IMapper;
use Drahak\Restful\Resource\Media;
use Nette\Http;
use stdClass;

/**
 * FileResponse
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class FileResponse extends BaseResponse
{

    public function __construct(
        protected Media $media,
        IMapper $mapper,
        ?string $contentType = NULL,
    )
    {
        parent::__construct($mapper, $contentType);
    }

    /**
     * Get response data
     */
    public function getData(): iterable|stdClass|string
    {
        return $this->media->getContent();
    }

    /**
     * Sends response to output
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->media->getContentType());
        echo $this->media;
    }
}
