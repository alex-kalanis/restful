<?php

namespace kalanis\Restful\Application\Responses;


use kalanis\Restful\Mapping\IMapper;
use kalanis\Restful\Resource\Media;
use Nette\Http;
use stdClass;


/**
 * FileResponse
 * @package kalanis\Restful\Application\Responses
 */
class FileResponse extends BaseResponse
{

    public function __construct(
        protected Media $media,
        IMapper         $mapper,
        ?string         $contentType = null,
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
