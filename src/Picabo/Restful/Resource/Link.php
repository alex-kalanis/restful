<?php

namespace Picabo\Restful\Resource;

use Nette;
use Picabo\Restful\IResource;
use Stringable;

/**
 * Link representation in resource
 * @package Picabo\Restful\Resource
 * @author Drahomír Hanák
 */
class Link implements IResource, Stringable
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

    public function __construct(
        private readonly string $href,
        private readonly string $rel = self::SELF,
    )
    {
    }

    /**
     * Get link URL
     * @return string
     */
    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * Create link with new href
     * @param string $href
     */
    public function setHref(string $href): self
    {
        return new Link($href, $this->rel);
    }

    /**
     * Get link rel
     * @return string
     */
    public function getRel(): string
    {
        return $this->rel;
    }

    /**
     * Create link with new rel
     * @param string $rel
     */
    public function setRel(string $rel): self
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
     */
    public function getData(): array
    {
        return [
            'href' => $this->href,
            'rel' => $this->rel,
        ];
    }
}
