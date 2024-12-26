<?php

namespace kalanis\Restful\Resource;


use Stringable;


/**
 * Media resource representation object
 * @package kalanis\Restful\Resource
 */
class Media implements Stringable
{

    private ?string $contentType;

    /**
     * @param string $content
     * @param string|null $contentType
     */
    public function __construct(
        private readonly string $content,
        ?string                 $contentType = null,
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

    /**
     * Get file
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
