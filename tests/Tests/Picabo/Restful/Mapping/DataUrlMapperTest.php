<?php

namespace Tests\Picabo\Restful\Mapping;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Mapping\DataUrlMapper;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Mapping\DataUrlMapper.
 *
 * @testCase Tests\Picabo\Restful\Mapping\DataUrlMapperTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Mapping
 */
class DataUrlMapperTest extends TestCase
{

    /** @var DataUrlMapper */
    private $mapper;

    private $media;

    public function testEncodeContentToBase64WithMimeTypeFromMediaObject(): void
    {
        $this->media->expects('__toString')
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
        $this->media = Mockery::mock(\Picabo\Restful\Resource\Media::class);
        $this->mapper = new DataUrlMapper;
    }

}

(new DataUrlMapperTest())->run();
