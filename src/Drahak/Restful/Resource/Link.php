<?php

namespace Drahak\Restful\Resource;

use Drahak\Restful\IResource;
use Nette;

/**
 * Link representation in resource
 * @package Drahak\Restful\Resource
 * @author Drahomír Hanák
 *
 * @property-read string $href
 * @property-read string $rel
 */
class Link implements IResource, \Stringable
{
    use Nette\SmartObject;

    /** Link pointing on self */
    public const SELF = 'self';
    /** Link pointing on next page */
    public const NEXT = 'next';
    /** Link pointing on previous page */
    public const PREVIOUS = 'prev';
    /** Link pointing on last page */
    public const LAST = 'last';

    /**
     * @param string $href
     * @param string $rel
     */
    public function __construct(private $href, private $rel = self::SELF)
    {
    }

    /**
     * Get link URL
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Create link with new href
     * @param string $href
     */
    public function setHref($href): self
    {
        return new Link($href, $this->rel);
    }

    /**
     * Get link rel
     * @return string
     */
    public function getRel()
    {
        return $this->rel;
    }

    /**
     * Create link with new rel
     * @param string $rel
     */
    public function setRel($rel): self
    {
        return new Link($this->href, $rel);
    }

    /**
     * Converts link to string
     */
    public function __toString(): string
    {
        return '<' . $this->href . '>;rel="' . $this->rel . '"';
    }

    /****************** Resource element interface ******************/

    /**
     * Get element value or array data
     * @return mixed
     */
    public function getData()
    {
        return ['href' => $this->href, 'rel' => $this->rel];
    }


}
