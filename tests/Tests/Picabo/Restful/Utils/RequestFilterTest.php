<?php

namespace Tests\Picabo\Restful\Utils;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockista\MockInterface;
use Nette;
use Picabo\Restful\Utils\RequestFilter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Utils\RequestFilter.
 *
 * @testCase Tests\Picabo\Restful\Utils\RequestFilterTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Utils
 */
class RequestFilterTest extends TestCase
{

    /** @var MockInterface */
    private $request;

    /** @var RequestFilter */
    private $filter;

    public function testGetFieldsListFromString(): void
    {
        $this->request->expects('getQuery')
            ->once()
            ->with(RequestFilter::FIELDS_KEY)
            ->andReturn('-any,item,list,');

        $result = $this->filter->getFieldList();
        Assert::type('array', $result);
        Assert::same($result, array('-any', 'item', 'list'));
    }

    public function testGetFieldListFromArrayInUrl(): void
    {
        $fields = array('-any', 'item', 'list');
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

        $expected = array(
            'any' => RequestFilter::SORT_DESC,
            'item' => RequestFilter::SORT_ASC,
            'list' => RequestFilter::SORT_ASC
        );

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
            ->with('offset', NULL)
            ->andReturn(20);
        $this->request->expects('getQuery')
            ->once()
            ->with('limit', NULL)
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
            ->with('offset', NULL)
            ->andReturn(20);
        $this->request->expects('getQuery')
            ->once()
            ->with('limit', NULL)
            ->andReturn(NULL);

        Assert::throws(function () {
            $this->filter->getPaginator();
        }, \Picabo\Restful\Exceptions\InvalidStateException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = $this->mockista->create(\Nette\Http\IRequest::class);
        $this->filter = new RequestFilter($this->request);
    }

}

(new RequestFilterTest())->run();
