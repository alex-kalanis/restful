<?php

namespace Picabo\Restful\Application\Responses;

use Nette\Application\Response;
use Picabo;
use stdClass;

/**
 * IResponse
 * @package Picabo\Restful\Application\Responses
 */
interface IResponse extends Response
{
    public function getContentType(): ?string;

    /**
     * @return iterable<string|int, mixed>|stdClass|string
     */
    public function getData(): iterable|stdClass|string;
}
