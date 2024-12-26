<?php

namespace kalanis\Restful\Application\Responses;


use kalanis\Restful\Exceptions\InvalidArgumentException;
use kalanis\Restful\Mapping\IMapper;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Strings;


/**
 * JSONP response
 * @package kalanis\Restful\Application\Responses
 */
class JsonpResponse extends BaseResponse
{

    /**
     * @param array<mixed> $data
     * @param IMapper $mapper
     * @param string|null $contentType
     */
    public function __construct(
        array   $data,
        IMapper $mapper,
        ?string $contentType = null,
    )
    {
        parent::__construct($mapper, $contentType);
        $this->data = $data;
    }

    /**
     * Send JSONP response to output
     * @throws InvalidArgumentException
     */
    public function send(IRequest $httpRequest, IResponse $httpResponse): void
    {
        $httpResponse->setContentType($this->getContentType() ?: 'application/javascript', 'UTF-8');

        $data = [];
        $data['response'] = $this->data;
        $data['status'] = $httpResponse->getCode();
        $data['headers'] = $httpResponse->getHeaders();

        $callback = $httpRequest->getQuery('jsonp') ? Strings::webalize(strval($httpRequest->getQuery('jsonp')), null, false) : '';
        echo $callback . '(' . $this->mapper->stringify($data, $this->isPrettyPrint()) . ');';
    }
}
