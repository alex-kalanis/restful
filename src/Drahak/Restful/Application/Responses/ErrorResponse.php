<?php

namespace Drahak\Restful\Application\Responses;

use Nette;
use Nette\Application\IResponse;
use Nette\Http;
use stdClass;
use Traversable;

class ErrorResponse implements IResponse
{

    use Nette\SmartObject;

    /**
     * @param BaseResponse $response Wrapped response with data
     * @param int $errorCode
     */
    public function __construct(private BaseResponse $response, private $code = 500)
    {
    }

    /**
     * Get response data
     * @return array|stdClass|Traversable
     */
    public function getData()
    {
        return $this->response->getData();
    }

    /**
     * Get response content type
     * @return string
     */
    public function getContentType()
    {
        return $this->response->contentType;
    }

    /**
     * Get response data
     * @return array|stdClass|Traversable
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Sends response to output
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
    {
        $httpResponse->setCode($this->code);
        $this->response->send($httpRequest, $httpResponse);
    }

}
 