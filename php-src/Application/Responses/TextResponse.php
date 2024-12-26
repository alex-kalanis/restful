<?php

namespace kalanis\Restful\Application\Responses;


use kalanis\Restful\Mapping\IMapper;
use kalanis\Restful\Resource\Media;
use Nette\Http;
use stdClass;


/**
 * TextResponse
 * @package kalanis\Restful\Application\Responses
 */
class TextResponse extends BaseResponse
{

    /**
     * @param Media|stdClass|string|iterable<string|int, mixed> $media
     * @param IMapper $mapper
     * @param string|null $contentType
     */
    public function __construct(
        protected readonly Media|iterable|stdClass|string $media,
        IMapper                  $mapper,
        ?string                  $contentType = null,
    )
    {
        parent::__construct($mapper, $contentType);
    }

    /**
     * Get response data
     * @inheritDoc
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
