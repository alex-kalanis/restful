<?php

namespace Tests\Picabo\Restful\Application\Responses;

require_once __DIR__ . '/../../../../bootstrap.php';

use Picabo\Restful\Application\Responses\JsonpResponse;
use Mockista\MockInterface;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Responses\JsonpResponse.
 *
 * @testCase Tests\Picabo\Restful\Application\Responses\JsonpResponseTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Responses
 */
class JsonpResponseTest extends TestCase
{

    /** @var MockInterface */
    private $httpRequest;

    /** @var MockInterface */
    private $httpResponse;

    /** @var JsonpResponse */
    private $response;

    /** @var MockInterface */
    private $mapper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->httpRequest = $this->mockista->create(\Nette\Http\IRequest::class);
        $this->httpResponse = $this->mockista->create(\Nette\Http\IResponse::class);
        $this->mapper = $this->mockista->create(\Picabo\Restful\Mapping\IMapper::class);
        $this->response = new JsonpResponse(['test' => 'JSONP'], $this->mapper);
    }

    public function testResponseWithJSONP(): void
    {
        $output = '{"response":{"test":"JSONP"},"status":200,"headers":{"X-Testing":true}}';
        $headers = array('X-Testing' => true);

        $data = array();
        $data['response'] = array('test' => 'JSONP');
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
            ->with($data, TRUE)
            ->andReturn($output);

        ob_start();
        $this->response->send($this->httpRequest, $this->httpResponse);
        $content = ob_get_clean();

        Assert::same($content, 'callbackFn(' . $output . ');');
    }

    public function testWebalizeCallbackFunctionNameAndKeepUpperCase(): void
    {
        $output = '{"response":{"test":"JSONP"},"status":200,"headers":{"X-Testing":true}}';
        $headers = array('X-Testing' => true);

        $data = array();
        $data['response'] = array('test' => 'JSONP');
        $data['status'] = 200;
        $data['headers'] = $headers;

        $this->mapper->expects('stringify')
            ->once()
            ->with($data, TRUE)
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
