<?php

namespace Drahak\Restful\Application\Responses;

use Drahak;
use Drahak\Restful\Mapping\IMapper;
use Nette;
use Nette\Application\IResponse;
use stdClass;
use Traversable;

/**
 * BaseResponse
 * @package Drahak\Restful\Application\Responses
 * @author Drahomír Hanák
 *
 * @property-read string $contentType
 * @property-write IMapper $mapper
 */
abstract class BaseResponse implements IResponse
{
    use Nette\SmartObject;

    /** @var array|stdClass|Traversable */
    protected $data;

    /** @var IMapper */
    protected $mapper;

    /** @var boolean */
    private $prettyPrint = TRUE;

    public function __construct(IMapper $mapper, protected $contentType = NULL)
    {
        $this->mapper = $mapper;
    }

    /**
     * Is pretty print enabled
     * @return bool
     */
    public function isPrettyPrint()
    {
        return $this->prettyPrint;
    }

    /**
     * Set pretty print
     * @param boolean $pretty
     */
    public function setPrettyPrint($pretty)
    {
        $this->prettyPrint = (bool)$pretty;
        return $this;
    }

    /**
     * Get response content type
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get response data
     * @return array|stdClass|Traversable
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set mapper
     * @return BaseResponse
     */
    public function setMapper(IMapper $mapper)
    {
        $this->mapper = $mapper;
        return $this;
    }

}
