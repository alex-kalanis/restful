<?php

namespace Tests\Restful\Utils;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Utils\RequestFilter;
use Mockery;
use Nette;
use Tester\Assert;
use Tests\TestCase;


class RequestFilterTest extends TestCase
{

    private $request;

    private RequestFilter $filter;

    public function testGetFieldsListFromString(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with(RequestFilter::FIELDS_KEY)
            ->andReturn('-any,item,list,');

        $result = $this->filter->getFieldList();
        Assert::type('array', $result);
        Assert::same($result, ['-any', 'item', 'list']);
    }

    public function testGetFieldListFromArrayInUrl(): void
    {
        $fields = ['-any', 'item', 'list'];
        $this->request->expects('getQuery')
            ->once()
            ->with(RequestFilter::FIELDS_KEY)
            ->andReturn($fields);

        $result = $this->filter->getFieldList();
        Assert::type('array', $result);
        Assert::equal($result, $fields);
    }

    public function testGetSortList(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('sort')
            ->andReturn('-any,item,list,');

        $expected = [
            'any' => RequestFilter::SORT_DESC,
            'item' => RequestFilter::SORT_ASC,
            'list' => RequestFilter::SORT_ASC,
        ];

        $result = $this->filter->getSortList();
        Assert::type('array', $result);
        Assert::same($result, $expected);
    }

    public function testGetSearchQuery(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('q')
            ->andReturn('search string');

        Assert::equal($this->filter->getSearchQuery(), 'search string');
    }

    public function testCreatePaginator(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('offset')
            ->andReturn(20);
        $this->request->expects('getQuery')
            ->once()
            ->with('limit')
            ->andReturn(10);

        $paginator = $this->filter->getPaginator();
        Assert::true($paginator instanceof Nette\Utils\Paginator);
        Assert::equal($paginator->getItemsPerPage(), 10);
        Assert::equal($paginator->getPage(), 3);
        Assert::equal($paginator->getOffset(), 20);
    }

    public function testThrowsExceptionWhenOffsetOrLimitNotProvided(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with('offset')
            ->andReturn(20);

        $this->request->expects('getQuery')
            ->once()
            ->with('limit')
            ->andReturn(NULL);

        Assert::throws(function () {
            $this->filter->getPaginator();
        }, \kalanis\Restful\Exceptions\InvalidStateException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->filter = new RequestFilter($this->request);
    }
}


(new RequestFilterTest())->run();
