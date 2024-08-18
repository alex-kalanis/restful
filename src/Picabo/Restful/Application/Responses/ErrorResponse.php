<?php

namespace Picabo\Restful\Application\Responses;

use Nette;
use Nette\Http;
use stdClass;

class ErrorResponse implements IResponse
{

    use Nette\SmartObject;

    /**
     * @param IResponse $response Wrapped response with data
     */
    public function __construct(
        private readonly IResponse $response,
        private readonly int       $code = 500,
    )
    {
    }

    /**
     * Get response data
     */
    public function getData(): iterable|stdClass
    {
        return $this->response->getData();
    }

    /**
     * Get response content type
     */
    public function getContentType(): string
    {
        return $this->response->getContentType();
    }

    /**
     * Get response data
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Sends response to output
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
    {
        $httpResponse->setCode($this->code);
        $this->response->send($httpRequest, $httpResponse);
    }
}
 