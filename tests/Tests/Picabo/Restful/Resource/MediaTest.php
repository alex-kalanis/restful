<?php

namespace Tests\Picabo\Restful\Resource;

require_once __DIR__ . '/../../../bootstrap.php';

use Nette;
use Picabo\Restful\Resource\Media;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Resource\Media.
 *
 * @testCase Tests\Picabo\Restful\Resource\MediaTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Resource
 */
class MediaTest extends TestCase
{

    private Media $media;

    public function setUp(): void
    {
        parent::setUp();
        $this->media = new Media('Test file', 'text/plain');
    }

    public function testDetermineMediaMimeTypeIfNotSet(): void
    {
        $type = $this->media->getContentType();
        Assert::equal($type, 'text/plain');
    }

    public function testGetMediaContent(): void
    {
        $content = $this->media->getContent();
        $magic = (string) $this->media;

        Assert::equal($content, 'Test file');
        Assert::same($content, $magic);
    }

    public function testCreateMediaFromFile(): void
    {
        $media = Media::fromFile(__DIR__ . '/Media.data.txt', 'text/plain');
        Assert::equal(Nette\Utils\Strings::trim($media->getContent()), 'Test file');
        Assert::equal($media->getContentType(), 'text/plain');
    }

}

(new MediaTest())->run();
