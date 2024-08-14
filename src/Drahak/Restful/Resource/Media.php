<?php

namespace Drahak\Restful\Resource;

use Nette;
use Nette\Templating\Helpers;
use Nette\Utils\MimeTypeDetector;

/**
 * Media resource representation object
 * @package Drahak\Restful\Resource
 * @author Drahomír Hanák
 *
 * @property-read string $content
 * @property-read string $contentType
 */
class Media implements \Stringable
{
    use Nette\SmartObject;

    /** @var string|NULL */
    private $contentType;

    /**
     * @param string $content
     * @param string|NULL $contentType
     */
    public function __construct(private $content, $contentType = NULL)
    {
        $this->contentType = $contentType ?: MimeTypeDetector::fromString($this->content);
    }

    /**
     * Create media from file
     * @param string $filePath
     * @param string|NULL $mimeType
     */
    public static function fromFile($filePath, $mimeType = NULL): self
    {
        if (!$mimeType) {
            $mimeType = MimeTypeDetector::fromFile($filePath);
        }
        return new Media(file_get_contents($filePath), $mimeType);
    }

    /**
     * Get media mime type
     * @return NULL|string
     */
    public function getContentType()
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
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

}
