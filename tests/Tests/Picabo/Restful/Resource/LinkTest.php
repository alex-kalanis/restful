<?php

namespace Tests\Picabo\Restful\Resource;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Resource\Link;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Resource\Link.
 *
 * @testCase Tests\Picabo\Restful\Resource\LinkTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Resource
 */
class LinkTest extends TestCase
{

    /** @var Link */
    private $link;

    public function setUp(): void
    {
        parent::setUp();
        $this->link = new Link('http://resource', Link::LAST);
    }

    public function testGetResourceData(): void
    {
        $data = $this->link->getData();
        Assert::equal($data['href'], 'http://resource');
        Assert::equal($data['rel'], Link::LAST);
    }

    public function testStringRepresentation(): void
    {
        $link = (string)$this->link;
        Assert::equal($link, '<http://resource>;rel="last"');
    }

    public function testLinkImmutabilityThroughHrefSetter(): void
    {
        $link = $this->link->setHref('http://test');
        Assert::notSame($link, $this->link);
        Assert::equal($this->link->getHref(), 'http://resource');
        Assert::equal($link->getHref(), 'http://test');
    }

    public function testLinkImmutabilityThroughRelSetter(): void
    {
        $link = $this->link->setRel('test');
        Assert::notSame($link, $this->link);
        Assert::equal($this->link->getRel(), Link::LAST);
        Assert::equal($link->getRel(), 'test');
    }

}

(new LinkTest())->run();
