<?php

namespace kalanis\Restful\Security;


use kalanis\Restful\Http\IInput;


/**
 * Input fingerprint hash Calculator interface
 * @package kalanis\Restful\Security
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
