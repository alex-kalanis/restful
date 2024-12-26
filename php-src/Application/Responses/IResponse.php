<?php

namespace kalanis\Restful\Application\Responses;


use Nette\Application\Response;
use stdClass;


/**
 * IResponse
 * @package kalanis\Restful\Application\Responses
 */
interface IResponse extends Response
{
    public function getContentType(): ?string;

    /**
     * @return iterable<string|int, mixed>|stdClass|string
     */
    public function getData(): iterable|stdClass|string;
}
