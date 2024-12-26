<?php

namespace Tests\Restful\Application\Events;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use Exception;
use kalanis\Restful\Application\Events\MethodHandler;
use kalanis\Restful\Application\Exceptions\BadRequestException;
use kalanis\Restful\Application\MethodOptions;
use Mockery;
use Nette\Application\Application;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Routing\Router;
use Tester\Assert;
use Tests\TestCase;


class MethodHandlerTest extends TestCase
{

    /** @var MethodHandler */
    private $methodHandler;

    private $request;

    private $response;

    private $methodOptions;

    private $application;

    private $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->methodOptions = Mockery::mock(MethodOptions::class);
        $this->request = Mockery::mock(IRequest::class);
        $this->request->expects('getMethod')->andReturn('METHOD');
        $this->response = Mockery::mock(IResponse::class);
        $this->application = Mockery::mock(Application::class);
        $this->router = Mockery::mock(Router::class);

        $this->methodHandler = new MethodHandler($this->request, $this->response, $this->methodOptions);
    }

    public function testPassesIfRouterMatchesCurrentRequest(): void
    {
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(['ok']);
        $this->methodHandler->run($this->application);
        Assert::true(true);
    }

    public function testPassesIfRouterDoesntMatchButThereAreNoAvailableMethods(): void
    {
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(null);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn([]);

        $this->methodHandler->run($this->application);
        Assert::true(true);
    }

    public function testThrowsExceptionIfRouteDoesntMatchAndThereAreAvailableMethods(): void
    {
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(null);
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn(['GET', 'POST']);
        $this->response->expects('setHeader')->once()->with('Allow', 'GET, POST');

        Assert::exception(function () {
            $this->methodHandler->run($this->application);
        }, \kalanis\Restful\Application\Exceptions\BadRequestException::class);
    }

    public function testPassesIfApplicationErrorAppearsButItIsNotBadRequestException(): void
    {
        $this->methodHandler->error($this->application, new Exception('Something went wrong.'));
        Assert::true(true);
    }

    public function testThrowsExceptionIfBadRequestExceptionAppears(): void
    {
        $url = Mockery::mock(\Nette\Http\UrlScript::class);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn(['PATCH', 'PUT']);
        $this->response->expects('setHeader')->once()->with('Allow', 'PATCH, PUT');

        Assert::exception(function () {
            $this->methodHandler->error($this->application, BadRequestException::notFound('Resource not found'));
        }, BadRequestException::class);
    }
}


(new MethodHandlerTest())->run();
