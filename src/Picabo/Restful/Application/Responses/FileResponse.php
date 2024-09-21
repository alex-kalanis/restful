<?php

namespace Picabo\Restful\Application\Responses;

use Nette\Http;
use Picabo\Restful\Mapping\IMapper;
use Picabo\Restful\Resource\Media;
use stdClass;

/**
 * FileResponse
 * @package Picabo\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class FileResponse extends BaseResponse
{

    public function __construct(
        protected Media $media,
        IMapper         $mapper,
        ?string         $contentType = NULL,
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
        $httpResponse->setContentType(strval($this->media->getContentType()));
        echo $this->media;
    }
}
