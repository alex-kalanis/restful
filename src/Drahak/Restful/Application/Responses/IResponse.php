<?php

namespace Drahak\Restful\Application\Responses;

use Drahak;
use Nette\Application\Response;
use stdClass;

/**
 * IResponse
 * @package Drahak\Restful\Application\Responses
 */
interface IResponse extends Response
{
    public function getContentType(): string;

    public function getData(): iterable|stdClass|string;
}
