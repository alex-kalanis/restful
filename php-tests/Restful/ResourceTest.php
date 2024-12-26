<?php

namespace Tests\Restful;

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Resource;
use Tester\Assert;
use Tests\TestCase;


class ResourceTest extends TestCase
{
    private Resource $resource;

    public function testAddingDataThroughArrayAccess(): void
    {
        $this->resource['name'] = 'Test';
        $data = $this->resource->getData();
        Assert::equal($data['name'], 'Test');
    }

    public function testAddingArrayListThroughArrayAccess(): void
    {
        $this->resource[] = 'hello';
        $this->resource[] = 'world';
        $data = $this->resource->getData();
        Assert::equal($data[0], 'hello');
        Assert::equal($data[1], 'world');
    }

    public function testAddingDataThroughMagicMethods(): void
    {
        $this->resource->name = 'Test';
        Assert::equal($this->resource->name, 'Test');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->resource = new Resource();
    }
}


(new ResourceTest())->run();
