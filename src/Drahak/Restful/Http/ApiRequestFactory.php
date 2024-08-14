<?php

namespace Drahak\Restful\Http;

use Nette\Http\IRequest;
use Nette\Http\Request;
use Nette\Http\RequestFactory;

/**
 * Api request factory
 * @author Drahomír Hanák
 */
class ApiRequestFactory
{

    public const OVERRIDE_HEADER = 'X-HTTP-Method-Override';
    public const OVERRIDE_PARAM = '__method';

    public function __construct(private readonly RequestFactory $factory)
    {
    }

    /**
     * Create API HTTP request
     * @return IRequest
     */
    public function createHttpRequest()
    {
        $request = $this->factory->createHttpRequest();
        $url = $request->getUrl();
        $url->setQuery($request->getQuery());

        return new Request(
            $url, NULL, $request->getPost(), $request->getFiles(), $request->getCookies(), $request->getHeaders(),
            $this->getPreferredMethod($request), $request->getRemoteAddress(), null,
            fn(): ?string => $request->getRawBody()
        );
    }

    /**
     * Get prederred method
     * @return string
     */
    protected function getPreferredMethod(IRequest $request)
    {
        $method = $request->getMethod();
        $isPost = $method === IRequest::POST;
        $header = $request->getHeader(self::OVERRIDE_HEADER);
        $param = $request->getQuery(self::OVERRIDE_PARAM);
        if ($header && $isPost) {
            return $header;
        }
        if ($param && $isPost) {
            return $param;
        }
        return $request->getMethod();
    }

}
