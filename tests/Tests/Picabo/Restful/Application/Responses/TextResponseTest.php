<?php

namespace Tests\Picabo\Restful\Application\Responses;

require_once __DIR__ . '/../../../../bootstrap.php';

use Picabo\Restful\Application\Responses\TextResponse;
use Mockery;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Responses\TextResponse.
 *
 * @testCase Tests\Picabo\Restful\Application\Responses\TextResponseTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Responses
 */
class TextResponseTest extends TestCase
{

    private $mapper;

    /** @var TextResponse */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = Mockery::mock(\Picabo\Restful\Mapping\IMapper::class);
        $this->response = new TextResponse(['hello' => 'world'], $this->mapper, 'application/json');
    }

    public function testResponseWithJson(): void
    {
        $output = '{"hello":"world"}';

        $this->mapper->expects('stringify')
            ->once()
            ->with(array('hello' => 'world'), TRUE)
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
