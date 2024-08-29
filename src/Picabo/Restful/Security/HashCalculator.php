<?php

namespace Picabo\Restful\Security;

use Nette;
use Nette\Http\IRequest;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Http\IInput;
use Picabo\Restful\Mapping\IMapper;
use Picabo\Restful\Mapping\MapperContext;

/**
 * Default auth token calculator implementation
 * @package Picabo\Restful\Security
 * @author Drahomír Hanák
 */
class HashCalculator implements IAuthTokenCalculator
{
    use Nette\SmartObject;

    /** Fingerprint hash algorithm */
    public const HASH = 'sha256';

    private string $privateKey = '';

    private IMapper $mapper;

    public function __construct(
        MapperContext $mapperContext,
        IRequest      $httpRequest,
    )
    {
        $this->mapper = $mapperContext->getMapper($httpRequest->getHeader('content-type'));
    }

    /**
     * Set hash data calculator mapper
     */
    public function setMapper(IMapper $mapper): static
    {
        $this->mapper = $mapper;
        return $this;
    }

    /**
     * Set hash calculator security private key
     * @param string $key
     * @return IAuthTokenCalculator
     */
    public function setPrivateKey(#[\SensitiveParameter] string $key): static
    {
        $this->privateKey = $key;
        return $this;
    }

    /**
     * Calculates hash
     *
     * @throws InvalidStateException
     */
    public function calculate(IInput $input): string
    {
        if (empty($this->privateKey)) {
            throw new InvalidStateException('Private key is not set');
        }

        $dataString = $this->mapper->stringify($input->getData(), false);
        return hash_hmac(self::HASH, $dataString, $this->privateKey);
    }

}
