<?php

namespace kalanis\Restful\Application\Responses;


use Nette\Http;
use stdClass;


/**
 * NullResponse
 * @package kalanis\Restful\Responses
 */
class NullResponse implements IResponse
{

    public function getContentType(): ?string
    {
        return null;
    }

    public function getData(): iterable|stdClass|string
    {
        return '';
    }

    /**
     * Do nothing
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse): void
    {
    }
}
