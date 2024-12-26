<?php

namespace kalanis\Restful\Security;


use kalanis\Restful\Exceptions\InvalidStateException;
use kalanis\Restful\Http\IInput;
use kalanis\Restful\Mapping\IMapper;
use kalanis\Restful\Mapping\MapperContext;
use Nette\Http\IRequest;


/**
 * Default auth token calculator implementation
 * @package kalanis\Restful\Security
 */
class HashCalculator implements IAuthTokenCalculator
{

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
     * @return $this
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
