<?php

namespace Picabo\Restful\Application\Responses;

use Nette;
use Nette\Http;
use stdClass;

/**
 * NullResponse
 * @package Picabo\Restful\Responses
 * @author Drahomír Hanák
 */
class NullResponse implements IResponse
{
    use Nette\SmartObject;

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
