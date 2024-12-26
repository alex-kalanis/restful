<?php

namespace Tests\Restful\Application\Responses;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\Responses\JsonpResponse;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class JsonpResponseTest extends TestCase
{

    private $httpRequest;

    private $httpResponse;

    private JsonpResponse $response;

    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpRequest = Mockery::mock(\Nette\Http\IRequest::class);
        $this->httpResponse = Mockery::mock(\Nette\Http\IResponse::class);
        $this->mapper = Mockery::mock(\kalanis\Restful\Mapping\IMapper::class);
        $this->response = new JsonpResponse(['test' => 'JSONP'], $this->mapper);
    }

    public function testResponseWithJSONP(): void
    {
        $output = '{"response":{"test":"JSONP"},"status":200,"headers":{"X-Testing":true}}';
        $headers = ['X-Testing' => true];

        $data = [];
        $data['response'] = ['test' => 'JSONP'];
        $data['status'] = 200;
        $data['headers'] = $headers;

        $this->httpResponse->expects('setContentType')
            ->once()
            ->with('application/javascript', 'UTF-8');
        $this->httpResponse->expects('getCode')
            ->once()
            ->andReturn(200);
        $this->httpResponse->expects('getHeaders')
            ->once()
            ->andReturn($headers);
        $this->httpRequest->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn('callbackFn');

        $this->mapper->expects('stringify')
            ->once()
            ->with($data, true)
            ->andReturn($output);

        ob_start();
        $this->response->send($this->httpRequest, $this->httpResponse);
        $content = ob_get_clean();

        Assert::same($content, 'callbackFn(' . $output . ');');
    }

    public function testWebalizeCallbackFunctionNameAndKeepUpperCase(): void
    {
        $output = '{"response":{"test":"JSONP"},"status":200,"headers":{"X-Testing":true}}';
        $headers = ['X-Testing' => true];

        $data = [];
        $data['response'] = ['test' => 'JSONP'];
        $data['status'] = 200;
        $data['headers'] = $headers;

        $this->mapper->expects('stringify')
            ->once()
            ->with($data, true)
            ->andReturn($output);

        $this->httpResponse->expects('setContentType')
            ->once()
            ->with('application/javascript', 'UTF-8');
        $this->httpResponse->expects('getCode')
            ->once()
            ->andReturn(200);
        $this->httpResponse->expects('getHeaders')
            ->once()
            ->andReturn($headers);
        $this->httpRequest->expects('getQuery')
            ->once()
            ->with('jsonp')
            ->andReturn('ěščřžýáíéAnd+_-! ?');

        ob_start();
        $this->response->send($this->httpRequest, $this->httpResponse);
        $content = ob_get_contents();
        ob_end_flush();

        Assert::same($content, 'escrzyaieAnd(' . $output . ');');
    }
}


(new JsonpResponseTest())->run();
