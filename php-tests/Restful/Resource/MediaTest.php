<?php

namespace Tests\Restful\Resource;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Resource\Media;
use Nette;
use Tester\Assert;
use Tests\TestCase;


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
        $media = Media::fromFile(__DIR__ . DIRECTORY_SEPARATOR . 'Media.data.txt', 'text/plain');
        Assert::equal(Nette\Utils\Strings::trim($media->getContent()), 'Test file');
        Assert::equal($media->getContentType(), 'text/plain');
    }
}


(new MediaTest())->run();
