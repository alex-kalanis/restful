<?php

namespace Tests\Picabo\Restful\Application\Events;

require_once __DIR__ . '/../../../../bootstrap.php';

use Exception;
use Nette\Application\Application;
use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Routing\Router;
use Picabo\Restful\Application\Events\MethodHandler;
use Mockista\MockInterface;
use Picabo\Restful\Application\Exceptions\BadRequestException;
use Picabo\Restful\Application\MethodOptions;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Events\MethodHandler.
 *
 * @testCase Tests\Picabo\Restful\Application\Events\MethodHandlerTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Events
 */
class MethodHandlerTest extends TestCase
{

    /** @var MethodHandler */
    private $methodHandler;

    /** @var MockInterface */
    private $request;

    /** @var MockInterface */
    private $response;

    /** @var MockInterface */
    private $methodOptions;

    /** @var MockInterface */
    private $application;

    /** @var MockInterface */
    private $router;

    protected function setUp(): void
    {
        parent::setUp();
        $this->methodOptions = $this->mockista->create(MethodOptions::class);
        $this->request = $this->mockista->create(IRequest::class);
        $this->request->method = 'METHOD';
        $this->response = $this->mockista->create(IResponse::class);
        $this->application = $this->mockista->create(Application::class);
        $this->router = $this->mockista->create(Router::class);

        $this->methodHandler = new MethodHandler($this->request, $this->response, $this->methodOptions);
    }

    protected function tearDown(): void
    {
        $this->mockista->assertExpectations();
    }

    public function testPassesIfRouterMatchesCurrentRequest(): void
    {
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(TRUE);
        $this->methodHandler->run($this->application);
        Assert::true(true);
    }

    public function testPassesIfRouterDoesntMatchButThereAreNoAvailableMethods(): void
    {
        $url = $this->mockista->create(\Nette\Http\UrlScript::class);
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(FALSE);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn(array());

        $this->methodHandler->run($this->application);
        Assert::true(true);
    }

    public function testThrowsExceptionIfRouteDoesntMatchAndThereAreAvailableMethods(): void
    {
        $this->application->expects('getRouter')->once()->andReturn($this->router);
        $this->router->expects('match')->once()->with($this->request)->andReturn(FALSE);
        $url = $this->mockista->create(\Nette\Http\UrlScript::class);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn(array('GET', 'POST'));
        $this->response->expects('setHeader')->once()->with('Allow', 'GET, POST');

        Assert::exception(function () {
            $this->methodHandler->run($this->application);
        }, \Picabo\Restful\Application\Exceptions\BadRequestException::class);
    }

    public function testPassesIfApplicationErrorAppearsButItIsNotBadRequestException(): void
    {
        $this->methodHandler->error($this->application, new Exception('Something went wrong.'));
        Assert::true(true);
    }

    public function testThrowsExceptionIfBadRequestExceptionAppears(): void
    {
        $url = $this->mockista->create(\Nette\Http\UrlScript::class);
        $this->request->expects('getUrl')->once()->andReturn($url);
        $this->methodOptions->expects('getOptions')->once()->with($url)->andReturn(array('PATCH', 'PUT'));
        $this->response->expects('setHeader')->once()->with('Allow', 'PATCH, PUT');

        Assert::exception(function () {
            $this->methodHandler->error($this->application, BadRequestException::notFound('Resource not found'));
        }, BadRequestException::class);
    }

}

(new MethodHandlerTest())->run();
