<?php

namespace Picabo\Restful\Resource;

use Nette;
use Stringable;

/**
 * Media resource representation object
 * @package Picabo\Restful\Resource
 * @author Drahomír Hanák
 */
class Media implements Stringable
{
    use Nette\SmartObject;

    private ?string $contentType;

    /**
     * @param string $content
     * @param string|NULL $contentType
     */
    public function __construct(
        private readonly string $content,
        ?string                 $contentType = NULL
    )
    {
        $this->contentType = $contentType ?: $this->initContentType();
    }

    private function initContentType(): ?string
    {
        $type = mime_content_type('data://,' . urlencode($this->content));
        return $type ?: null;
    }

    /**
     * Create media from file
     */
    public static function fromFile(string $filePath, ?string $mimeType = null): self
    {
        if (empty($mimeType)) {
            $mimeType = mime_content_type($filePath);
        }
        return new Media(strval(file_get_contents($filePath)), strval($mimeType));
    }

    /**
     * Get media mime type
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * Converts media to string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }

    /******************** Media simple factory ********************/

    /**
     * Get file
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
