<?php

namespace kalanis\Restful\Application\Events;


use Exception;
use kalanis\Restful\Application\Exceptions\BadRequestException;
use kalanis\Restful\Application\MethodOptions;
use Nette\Application\Application;
use Nette\Application\BadRequestException as NetteBadRequestException;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Throwable;


/**
 * MethodHandler
 * @package kalanis\Restful\Application
 */
class MethodHandler
{

    public function __construct(
        private readonly IRequest      $request,
        private readonly IResponse     $response,
        private readonly MethodOptions $methods,
    )
    {
    }

    /**
     * On application run
     *
     * @throws BadRequestException
     */
    public function run(Application $application): void
    {
        $router = $application->getRouter();
        $appRequest = $router->match($this->request);
        if (!$appRequest) {
            $this->checkAllowedMethods();
        }
    }

    /**
     * Check allowed methods
     *
     * @throws BadRequestException If method is not supported but another one can be used
     */
    protected function checkAllowedMethods(): void
    {
        $availableMethods = $this->methods->getOptions($this->request->getUrl());
        if (!$availableMethods || in_array($this->request->getMethod(), $availableMethods)) {
            return;
        }

        $allow = implode(', ', $availableMethods);
        $this->response->setHeader('Allow', $allow);
        throw BadRequestException::methodNotSupported('Method not supported. Available methods: ' . $allow);
    }

    /**
     * On application error
     * @param Exception|Throwable $e
     */
    public function error(Application $application, $e): void
    {
        if ($e instanceof NetteBadRequestException && 404 === $e->getCode()) {
            $this->checkAllowedMethods();
        }
    }
}
