<?php

namespace Picabo\Restful\Application\Responses;

use Nette\Http;
use Picabo\Restful\Mapping\IMapper;
use Picabo\Restful\Resource\Media;
use stdClass;

/**
 * TextResponse
 * @package Picabo\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class TextResponse extends BaseResponse
{

    public function __construct(
        protected readonly Media|iterable|stdClass|string $media,
        IMapper                  $mapper,
        ?string                  $contentType = NULL,
    )
    {
        parent::__construct($mapper, $contentType);
    }

    /**
     * Get response data
     */
    public function getData(): iterable|stdClass|string
    {
        return ($this->media instanceof Media)
            ? $this->media->getContent()
            : $this->media;
    }

    /**
     * Sends response to output
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->contentType ?: 'text/plain', 'UTF-8');
        echo $this->mapper->stringify($this->media, $this->isPrettyPrint());
    }
}
