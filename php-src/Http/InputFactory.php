<?php

namespace kalanis\Restful\Http;


use kalanis\Restful\Application\Exceptions\BadRequestException;
use kalanis\Restful\Exceptions\InvalidStateException;
use kalanis\Restful\Mapping\Exceptions\MappingException;
use kalanis\Restful\Mapping\MapperContext;
use kalanis\Restful\Validation\IValidationScopeFactory;
use Nette;
use Nette\Http\IRequest;


/**
 * InputFactory
 * @package kalanis\Restful\Http
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
     * @return Input<string, mixed>
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
     * @return array<string, mixed>
     */
    protected function parseData(): array
    {
        $postQuery = (array) $this->httpRequest->getPost();
        $urlQuery = (array) $this->httpRequest->getQuery();
        $requestBody = $this->parseRequestBody();

        return array_merge($urlQuery, $postQuery, $requestBody);    // $requestBody must be the last one!!!
    }

    /**
     * Parse request body if any
     * @throws BadRequestException
     * @return array<string, mixed>
     */
    protected function parseRequestBody(): array
    {
        $requestBody = [];
        $input = $this->httpRequest->getRawBody();

        if ($input) {
            try {
                $mapper = $this->mapperContext->getMapper($this->httpRequest->getHeader('Content-Type'));
                $requestBody = (array) $mapper->parse($input);
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
