<?php

namespace Drahak\Restful\Application\Responses;

use Drahak\Restful\Mapping\IMapper;
use Drahak\Restful\Resource\Media;
use Nette\Http;
use stdClass;

/**
 * TextResponse
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class TextResponse extends BaseResponse
{

    public function __construct(
        protected readonly Media $media,
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
        $httpResponse->setContentType($this->contentType ?: 'text/plain', 'UTF-8');
        echo $this->mapper->stringify($this->media, $this->isPrettyPrint());
    }
}
