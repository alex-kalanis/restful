<?php

namespace kalanis\Restful\Http;


/**
 * REST client request Input interface
 * @package kalanis\Restful\Http
 */
interface IInput
{

    /**
     * Get parsed input data
     * @return array<string, mixed>
     */
    public function getData(): array;
}
