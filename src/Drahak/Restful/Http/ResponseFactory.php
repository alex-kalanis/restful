<?php

namespace Drahak\Restful\Http;

use Drahak\Restful\InvalidStateException;
use Drahak\Restful\Resource\Link;
use Drahak\Restful\Utils\RequestFilter;
use Nette;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Nette\Utils\Paginator;

/**
 * ResponseFactory
 * @package Drahak\Restful\Http
 * @author Drahomír Hanák
 */
class ResponseFactory
{
    use Nette\SmartObject;

    /** @var array Default response code for each request method */
    protected $defaultCodes = [IRequest::GET => 200, IRequest::POST => 201, IRequest::PUT => 200, IRequest::HEAD => 200, IRequest::DELETE => 200, 'PATCH' => 200];
    /** @var IResponse */
    private $response;

    public function __construct(private IRequest $request, private RequestFilter $requestFilter)
    {
    }

    /**
     * Set original wrapper response since nette does not support custom response codes
     */
    public function setResponse(IResponse $response): static
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Create HTTP response
     * @param int|NULL $code
     * @return IResponse
     */
    public function createHttpResponse($code = NULL)
    {
        $response = $this->response ?: new Response();
        $response->setCode($this->getCode($code));

        try {
            $response->setHeader('Link', $this->getPaginatorLink());
            $response->setHeader('X-Total-Count', $this->getPaginatorTotalCount());
        } catch (InvalidStateException) {
            // Don't use paginator
        }
        return $response;
    }

    /**
     * Get default status code
     * @param int|null $code
     */
    protected function getCode($code = NULL): int
    {
        if ($code === NULL) {
            $code = $code = $this->defaultCodes[$this->request->getMethod()] ?? 200;
        }
        return (int)$code;
    }

    /**
     * Get paginator next/last link header
     * @return string
     */
    protected function getPaginatorLink()
    {
        $paginator = $this->requestFilter->getPaginator();

        $link = $this->getNextPageUrl($paginator);
        if ($paginator->getItemCount()) {
            $link .= ', ' . $this->getLastPageUrl($paginator);
        }
        return $link;
    }

    /**
     * Get next page URL
     * @return Link
     */
    private function getNextPageUrl(Paginator $paginator)
    {
        $url = clone $this->request->getUrl();
        parse_str($url->getQuery(), $query);
        $paginator->setPage($paginator->getPage() + 1);
        $query['offset'] = $paginator->getOffset();
        $query['limit'] = $paginator->getItemsPerPage();
        $url->appendQuery($query);
        return new Link($url, Link::NEXT);
    }

    /**
     * Get last page URL
     * @return Link
     */
    private function getLastPageUrl(Paginator $paginator)
    {
        $url = clone $this->request->getUrl();
        parse_str($url->getQuery(), $query);
        $query['offset'] = $paginator->getLastPage() * $paginator->getItemsPerPage() - $paginator->getItemsPerPage();
        $query['limit'] = $paginator->getItemsPerPage();
        $url->appendQuery($query);
        return new Link($url, Link::LAST);
    }

    /**
     * Get paginator items total count
     * @return int|NULL
     */
    protected function getPaginatorTotalCount()
    {
        $paginator = $this->requestFilter->getPaginator();
        return $paginator->getItemCount() ?: NULL;
    }

}
