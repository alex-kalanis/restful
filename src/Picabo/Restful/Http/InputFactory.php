<?php

namespace Picabo\Restful\Http;

use Nette;
use Nette\Http\IRequest;
use Picabo\Restful\Application\Exceptions\BadRequestException;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Mapping\Exceptions\MappingException;
use Picabo\Restful\Mapping\MapperContext;
use Picabo\Restful\Validation\IValidationScopeFactory;

/**
 * InputFactory
 * @package Picabo\Restful\Http
 * @author Drahomír Hanák
 */
class InputFactory
{
    use Nette\SmartObject;

    public function __construct(
        protected readonly IRequest              $httpRequest,
        private readonly MapperContext           $mapperContext,
        private readonly IValidationScopeFactory $validationScopeFactory,
    )
    {
    }

    /**
     * Create input
     * @return Input
     */
    public function create(): Input
    {
        $input = new Input($this->validationScopeFactory);
        $input->setData($this->parseData());
        return $input;
    }

    /**
     * Parse data for input
     *
     * @throws BadRequestException
     */
    protected function parseData(): array
    {
        $postQuery = (array)$this->httpRequest->getPost();
        $urlQuery = (array)$this->httpRequest->getQuery();
        $requestBody = $this->parseRequestBody();

        return array_merge($urlQuery, $postQuery, $requestBody);    // $requestBody must be the last one!!!
    }

    /**
     * Parse request body if any
     * @return array
     * @throws BadRequestException
     */
    protected function parseRequestBody(): array
    {
        $requestBody = [];
        $input = $this->httpRequest->getRawBody();

        if ($input) {
            try {
                $mapper = $this->mapperContext->getMapper($this->httpRequest->getHeader('Content-Type'));
                $requestBody = (array)$mapper->parse($input);
            } catch (InvalidStateException $e) {
                throw BadRequestException::unsupportedMediaType(
                    'No mapper defined for Content-Type ' . $this->httpRequest->getHeader('Content-Type'),
                    $e
                );
            } catch (MappingException $e) {
                throw new BadRequestException($e->getMessage(), 400, $e);
            }
        }
        return $requestBody;
    }

}
