<?php

namespace Drahak\Restful\Http;

use Drahak\Restful\Application\BadRequestException;
use Drahak\Restful\InvalidStateException;
use Drahak\Restful\Mapping\IMapper;
use Drahak\Restful\Mapping\MapperContext;
use Drahak\Restful\Mapping\MappingException;
use Drahak\Restful\Validation\IValidationScopeFactory;
use Nette;
use Nette\Http\IRequest;
use Traversable;

/**
 * InputFactory
 * @package Drahak\Restful\Http
 * @author Drahomír Hanák
 */
class InputFactory
{
    use Nette\SmartObject;

    /** @var IRequest */
    protected $httpRequest;

    /** @var IMapper */
    private $mapper;

    public function __construct(IRequest $httpRequest, private MapperContext $mapperContext, private IValidationScopeFactory $validationScopeFactory)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * Create input
     * @return Input
     */
    public function create()
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
     * @return array|Traversable
     *
     * @throws BadRequestException
     */
    protected function parseRequestBody()
    {
        $requestBody = [];
        $input = class_exists(\Nette\Framework::class) && Nette\Framework::VERSION_ID <= 20200 ? // Nette 2.2.0 and/or newer
            file_get_contents('php://input') :
            $this->httpRequest->getRawBody();

        if ($input) {
            try {
                $this->mapper = $this->mapperContext->getMapper($this->httpRequest->getHeader('Content-Type'));
                $requestBody = $this->mapper->parse($input);
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
