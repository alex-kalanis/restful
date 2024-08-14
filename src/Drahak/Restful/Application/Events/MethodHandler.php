<?php

namespace Drahak\Restful\Application\Events;

use Drahak\Restful\Application\Exceptions\BadRequestException;
use Drahak\Restful\Application\MethodOptions;
use Exception;
use Nette;
use Nette\Application\Application;
use Nette\Application\BadRequestException as NetteBadRequestException;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Throwable;

/**
 * MethodHandler
 * @package Drahak\Restful\Application
 * @author Drahomír Hanák
 */
class MethodHandler
{
    use Nette\SmartObject;

    public function __construct(
        private readonly IRequest $request,
        private readonly IResponse $response,
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
        if (!$availableMethods || in_array($this->request->method, $availableMethods)) {
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
        if ($e instanceof NetteBadRequestException && $e->getCode() === 404) {
            $this->checkAllowedMethods();
        }
    }
}
