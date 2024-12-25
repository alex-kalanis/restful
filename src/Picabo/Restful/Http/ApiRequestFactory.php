<?php

namespace Picabo\Restful\Http;

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

    public function __construct(
        private readonly RequestFactory $factory,
    )
    {
    }

    /**
     * Create API HTTP request
     * @return IRequest
     */
    public function createHttpRequest(): IRequest
    {
        $request = $this->factory->fromGlobals();
        $url = $request->getUrl()->withQuery($request->getQuery());

        return new Request(
            $url, (array) $request->getPost(), $request->getFiles(), $request->getCookies(), $request->getHeaders(),
            $this->getPreferredMethod($request), $request->getRemoteAddress(), null,
            fn(): ?string => $request->getRawBody()
        );
    }

    /**
     * Get preferred method
     */
    protected function getPreferredMethod(IRequest $request): string
    {
        $method = $request->getMethod();
        $isPost = IRequest::Post === $method;
        $header = $request->getHeader(self::OVERRIDE_HEADER);
        $param = $request->getQuery(self::OVERRIDE_PARAM);
        if ($header && $isPost) {
            return $header;
        }
        if ($param && $isPost) {
            return strval($param);
        }
        return $request->getMethod();
    }
}
