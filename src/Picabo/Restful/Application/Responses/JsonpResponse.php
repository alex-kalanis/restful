<?php

namespace Picabo\Restful\Application\Responses;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Strings;
use Picabo;
use Picabo\Restful\Exceptions\InvalidArgumentException;
use Picabo\Restful\Mapping\IMapper;

/**
 * JSONP response
 * @package Picabo\Restful\Application\Responses
 * @author Drahomír Hanák
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
        ?string $contentType = NULL,
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

        $callback = $httpRequest->getQuery('jsonp') ? Strings::webalize(strval($httpRequest->getQuery('jsonp')), NULL, FALSE) : '';
        echo $callback . '(' . $this->mapper->stringify($data, $this->isPrettyPrint()) . ');';
    }
}
