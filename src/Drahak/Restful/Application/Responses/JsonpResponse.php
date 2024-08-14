<?php

namespace Drahak\Restful\Application\Responses;

use Drahak;
use Drahak\Restful\Exceptions\InvalidArgumentException;
use Drahak\Restful\Mapping\IMapper;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Strings;

/**
 * JSONP response
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 */
class JsonpResponse extends BaseResponse
{

    public function __construct(
        array $data,
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

        $callback = $httpRequest->getQuery('jsonp') ? Strings::webalize($httpRequest->getQuery('jsonp'), NULL, FALSE) : '';
        echo $callback . '(' . $this->mapper->stringify($data, $this->isPrettyPrint()) . ');';
    }
}
