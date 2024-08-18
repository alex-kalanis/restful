<?php

namespace Picabo\Restful\Security;

use Picabo\Restful\Http\IInput;

/**
 * Input fingerprint hash Calculator interface
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
interface IAuthTokenCalculator
{

    /**
     * Set hash private key
     * @param string $key
     * @return IAuthTokenCalculator
     */
    public function setPrivateKey(#[\SensitiveParameter] string $key): self;

    /**
     * Calculate fingerprint hash
     * @param IInput $input
     * @return string
     */
    public function calculate(IInput $input): string;
}

