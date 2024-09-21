<?php

namespace Picabo\Restful\Http;

/**
 * REST client request Input interface
 * @package Picabo\Restful\Http
 * @author Drahomír Hanák
 */
interface IInput
{

    /**
     * Get parsed input data
     * @return array<string, mixed>
     */
    public function getData(): array;
}
