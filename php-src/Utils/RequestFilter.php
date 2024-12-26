<?php

namespace kalanis\Restful\Utils;


use kalanis\Restful\Exceptions\InvalidStateException;
use Nette\Http\IRequest;
use Nette\Utils\Paginator;


/**
 * RequestFilter
 * @package kalanis\Restful\Utils
 */
class RequestFilter
{

    /** Fields key in URL query */
    public const FIELDS_KEY = 'fields';
    /** Sort key in URL query */
    public const SORT_KEY = 'sort';
    /** Search string key in URL query */
    public const SEARCH_KEY = 'q';

    /** Descending sort */
    public const SORT_DESC = 'DESC';
    /** Ascending sort */
    public const SORT_ASC = 'ASC';

    /** @var array<string> */
    private array $fieldList = [];
    /** @var array<string, string> */
    private array $sortList = [];
    private ?Paginator $paginator = null;

    public function __construct(
        private readonly IRequest $request,
    )
    {
    }

    /**
     * Get fields list
     * @return array<string>
     */
    public function getFieldList(): array
    {
        if (empty($this->fieldList)) {
            $this->fieldList = $this->createFieldList();
        }
        return $this->fieldList;
    }

    /**
     * Create field list
     * @return array<string>
     */
    protected function createFieldList(): array
    {
        $fields = $this->request->getQuery(self::FIELDS_KEY);
        return is_string($fields)
            ? array_filter(explode(',', $fields))
            : array_filter(array_map(fn($val) => strval($val), (array) $fields))
        ;
    }

    /**
     * Create sort list
     * @return array<string, string>
     */
    public function getSortList(): array
    {
        if (empty($this->sortList)) {
            $this->sortList = $this->createSortList();
        }
        return $this->sortList;
    }

    /**
     * Create sort list
     * @return array<string, string>
     */
    protected function createSortList(): array
    {
        $sortList = [];
        $fields = array_filter(explode(',', strval($this->request->getQuery(self::SORT_KEY))));
        foreach ($fields as $field) {
            $isInverted = '-' === Strings::substring($field, 0, 1);
            $sort = $isInverted ? self::SORT_DESC : self::SORT_ASC;
            $field = $isInverted ? Strings::substring($field, 1) : $field;
            $sortList[$field] = $sort;
        }
        return $sortList;
    }

    /**
     * Get search query
     * @return mixed
     */
    public function getSearchQuery(): mixed
    {
        return $this->request->getQuery('q');
    }

    /**
     * Get paginator
     * @param int|null $offset default value
     * @param int|null $limit default value
     * @throws InvalidStateException
     * @return Paginator
     */
    public function getPaginator(?int $offset = null, ?int $limit = null): Paginator
    {
        if (empty($this->paginator)) {
            $this->paginator = $this->createPaginator($offset, $limit);
        }
        return $this->paginator;
    }

    /**
     * Create paginator
     * @param int|null $offset
     * @param int|null $limit
     * @throws InvalidStateException
     * @return Paginator
     */
    protected function createPaginator(?int $offset = null, ?int $limit = null): Paginator
    {
        $off = $this->request->getQuery('offset');
        if (empty($off)) {
            $off = $offset;
        }
        $lim = $this->request->getQuery('limit');
        if (empty($lim)) {
            $lim = $limit;
        }

        if (is_null($off) || is_null($lim)) {
            throw new InvalidStateException(
                'To create paginator add offset and limit query parameter to request URL'
            );
        }
        $lim = intval($lim);
        $off = intval($off);

        if (0 == $lim) {
            throw new InvalidStateException(
                'Pagination limit cannot be zero'
            );
        }

        $paginator = new Paginator();
        $paginator->setItemsPerPage($lim);
        $paginator->setPage(intval(floor($off / $lim)) + 1);
        return $paginator;
    }
}
