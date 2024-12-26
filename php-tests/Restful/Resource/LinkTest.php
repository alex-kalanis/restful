<?php

namespace Tests\Restful\Resource;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Resource\Link;
use Tester\Assert;
use Tests\TestCase;


class LinkTest extends TestCase
{

    private Link $link;

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
        $link = (string) $this->link;
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
