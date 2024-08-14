<?php

namespace Drahak\Restful\Utils;

use Drahak\Restful\InvalidStateException;
use Nette;
use Nette\Http\IRequest;
use Nette\Utils\Paginator;

/**
 * RequestFilter
 * @package Drahak\Restful\Utils
 * @author Drahomír Hanák
 *
 * @property-read array $fieldList
 * @property-read array $sortList
 * @property-read string $searchQuery
 * @property-read Paginator $paginator
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
    private $fieldList;

    /** @var array */
    private $sortList;

    /** @var Paginator */
    private $paginator;

    public function __construct(private IRequest $request)
    {
    }

    /**
     * Get fields list
     * @return array
     */
    public function getFieldList()
    {
        if (!$this->fieldList) {
            $this->fieldList = $this->createFieldList();
        }
        return $this->fieldList;
    }

    /**
     * Create field list
     * @return array
     */
    protected function createFieldList()
    {
        $fields = $this->request->getQuery(self::FIELDS_KEY);
        return is_string($fields) ? array_filter(explode(',', $fields)) : $fields;
    }

    /**
     * Create sort list
     * @return array
     */
    public function getSortList()
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
        $fields = array_filter(explode(',', (string) $this->request->getQuery(self::SORT_KEY)));
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
    public function getSearchQuery()
    {
        return $this->request->getQuery('q');
    }

    /**
     * Get paginator
     * @param string|NULL $offset default value
     * @param string|NULL $limit default value
     * @return Paginator
     *
     * @throws InvalidStateException
     */
    public function getPaginator($offset = NULL, $limit = NULL)
    {
        if (!$this->paginator) {
            $this->paginator = $this->createPaginator($offset, $limit);
        }
        return $this->paginator;
    }

    /**
     * Create paginator
     * @param int|null $offset
     * @param int|null $limit
     * @return Paginator
     *
     * @throws InvalidStateException
     */
    protected function createPaginator($offset = NULL, $limit = NULL)
    {
        $offset = $this->request->getQuery('offset', $offset);
        $limit = $this->request->getQuery('limit', $limit);

        if ($offset === NULL || $limit === NULL) {
            throw new InvalidStateException(
                'To create paginator add offset and limit query parameter to request URL'
            );
        }

        if ($limit == 0) {
            throw new InvalidStateException(
                'Pagination limit cannot be zero'
            );
        }

        $paginator = new Paginator();
        $paginator->setItemsPerPage($limit);
        $paginator->setPage(floor($offset / $limit) + 1);
        return $paginator;
    }

}
