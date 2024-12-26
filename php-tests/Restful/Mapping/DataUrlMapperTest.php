<?php

namespace Tests\Restful\Mapping;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Mapping\DataUrlMapper;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class DataUrlMapperTest extends TestCase
{

    private DataUrlMapper $mapper;

    private $media;

    public function testEncodeContentToBase64WithMimeTypeFromMediaObject(): void
    {
        $this->media->expects('getContent')
            ->once()
            ->andReturn('Hello world');
        $this->media->expects('getContentType')
            ->once()
            ->andReturn('text/plain');

        $encoded = $this->mapper->stringify($this->media);
        Assert::equal($encoded, 'data:text/plain;base64,SGVsbG8gd29ybGQ=');
    }

    public function testDecodeBase64DataToMediaObject(): void
    {
        $result = $this->mapper->parse('data:text/plain;base64,SGVsbG8gd29ybGQ=');
        Assert::equal($result->getContent(), 'Hello world');
        Assert::equal($result->getContentType(), 'text/plain');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->media = Mockery::mock(\kalanis\Restful\Resource\Media::class);
        $this->mapper = new DataUrlMapper;
    }
}


(new DataUrlMapperTest())->run();
