<?php

namespace Drahak\Restful\Application\Responses;

use Nette;
use Nette\Application\IResponse;
use Nette\Http;

/**
 * NullResponse
 * @package Drahak\Restful\Responses
 * @author Drahomír Hanák
 */
class NullResponse implements IResponse
{
    use Nette\SmartObject;

    /**
     * Do nothing
     */
    public function send(Http\IRequest $httpRequest, Http\IResponse $httpResponse)
    {
    }


}