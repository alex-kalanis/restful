<?php

namespace Tests\Restful\Application\Responses;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\Responses\TextResponse;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class TextResponseTest extends TestCase
{

    private $mapper;

    /** @var TextResponse */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = Mockery::mock(\kalanis\Restful\Mapping\IMapper::class);
        $this->response = new TextResponse(['hello' => 'world'], $this->mapper, 'application/json');
    }

    public function testResponseWithJson(): void
    {
        $output = '{"hello":"world"}';

        $this->mapper->expects('stringify')
            ->once()
            ->with(['hello' => 'world'], true)
            ->andReturn($output);

        $httpRequest = Mockery::mock(\Nette\Http\IRequest::class);
        $httpResponse = Mockery::mock(\Nette\Http\IResponse::class);

        $httpResponse->expects('setContentType')
            ->once()
            ->with('application/json', 'UTF-8');

        ob_start();
        $this->response->send($httpRequest, $httpResponse);
        $content = ob_get_clean();

        Assert::same($content, $output);
    }
}


(new TextResponseTest())->run();
