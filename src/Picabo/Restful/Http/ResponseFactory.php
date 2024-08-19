<?php

namespace Picabo\Restful\Http;

use Nette;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Nette\Utils\Paginator;
use Picabo\Restful\Exceptions\InvalidStateException;
use Picabo\Restful\Resource\Link;
use Picabo\Restful\Utils\RequestFilter;

/**
 * ResponseFactory
 * @package Picabo\Restful\Http
 * @author Drahomír Hanák
 */
class ResponseFactory
{
    use Nette\SmartObject;

    /** @var array<string, int> Default response code for each request method */
    protected array $defaultCodes = [
        IRequest::Get => 200,
        IRequest::Post => 201,
        IRequest::Put => 200,
        IRequest::Head => 200,
        IRequest::Delete => 200,
        'PATCH' => 200,
    ];

    private ?IResponse $response = null;

    public function __construct(
        private readonly IRequest      $request,
        private readonly RequestFilter $requestFilter,
    )
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
    public function createHttpResponse(?int $code = NULL): IResponse
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
     * @return int
     */
    protected function getCode(?int $code = NULL): int
    {
        if ($code === NULL) {
            $code = $this->defaultCodes[$this->request->getMethod()] ?? 200;
        }
        return (int)$code;
    }

    /**
     * Get paginator next/last link header
     * @return string
     */
    protected function getPaginatorLink(): string
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
     */
    private function getNextPageUrl(Paginator $paginator): Link
    {
        $url = clone $this->request->getUrl();
        parse_str($url->getQuery(), $query);
        $paginator->setPage($paginator->getPage() + 1);
        $query['offset'] = $paginator->getOffset();
        $query['limit'] = $paginator->getItemsPerPage();
        return new Link($url->withQuery(http_build_query($query)), Link::NEXT);
    }

    /**
     * Get last page URL
     */
    private function getLastPageUrl(Paginator $paginator): Link
    {
        $url = clone $this->request->getUrl();
        parse_str($url->getQuery(), $query);
        $query['offset'] = $paginator->getLastPage() * $paginator->getItemsPerPage() - $paginator->getItemsPerPage();
        $query['limit'] = $paginator->getItemsPerPage();
        return new Link($url->withQuery(http_build_query($query)), Link::LAST);
    }

    /**
     * Get paginator items total count
     * @return int|NULL
     */
    protected function getPaginatorTotalCount(): ?int
    {
        $paginator = $this->requestFilter->getPaginator();
        return $paginator->getItemCount() ?: NULL;
    }
}
