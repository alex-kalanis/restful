<?php

namespace Picabo\Restful\Utils;

use Nette;
use Nette\Http\IRequest;
use Nette\Utils\Paginator;
use Picabo\Restful\Exceptions\InvalidStateException;

/**
 * RequestFilter
 * @package Picabo\Restful\Utils
 * @author Drahomír Hanák
 */
class RequestFilter
{
    use Nette\SmartObject;

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

    /** @var array */
    private array $fieldList = [];
    /** @var array */
    private array $sortList = [];
    private ?Paginator $paginator = null;

    public function __construct(
        private readonly IRequest $request,
    )
    {
    }

    /**
     * Get fields list
     * @return array
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
     * @return array
     */
    protected function createFieldList(): array
    {
        $fields = $this->request->getQuery(self::FIELDS_KEY);
        return is_string($fields) ? array_filter(explode(',', $fields)) : $fields;
    }

    /**
     * Create sort list
     * @return array
     */
    public function getSortList(): array
    {
        if (!$this->sortList) {
            $this->sortList = $this->createSortList();
        }
        return $this->sortList;
    }

    /**
     * Create sort list
     */
    protected function createSortList(): array
    {
        $sortList = [];
        $fields = array_filter(explode(',', (string)$this->request->getQuery(self::SORT_KEY)));
        foreach ($fields as $field) {
            $isInverted = Strings::substring($field, 0, 1) === '-';
            $sort = $isInverted ? self::SORT_DESC : self::SORT_ASC;
            $field = $isInverted ? Strings::substring($field, 1) : $field;
            $sortList[$field] = $sort;
        }
        return $sortList;
    }

    /**
     * Get search query
     * @return string|NULL
     */
    public function getSearchQuery(): mixed
    {
        return $this->request->getQuery('q');
    }

    /**
     * Get paginator
     * @param int|NULL $offset default value
     * @param int|NULL $limit default value
     * @return Paginator
     * @throws InvalidStateException
     */
    public function getPaginator(?int $offset = NULL, ?int $limit = NULL): Paginator
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
     * @return Paginator
     * @throws InvalidStateException
     */
    protected function createPaginator(?int $offset = NULL, ?int $limit = NULL): Paginator
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

        if ($lim == 0) {
            throw new InvalidStateException(
                'Pagination limit cannot be zero'
            );
        }

        $paginator = new Paginator();
        $paginator->setItemsPerPage($lim);
        $paginator->setPage(floor($off / $lim) + 1);
        return $paginator;
    }

}
