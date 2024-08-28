<?php

namespace Tests\Picabo\Restful\Application\Responses;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Application\Responses\NullResponse;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Responses\NullResponse.
 *
 * @testCase Tests\Picabo\Restful\Application\Responses\NullResponseTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Responses
 */
class NullResponseTest extends TestCase
{
    private NullResponse $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = new NullResponse;
    }

    public function testDoNotSendResponse(): void
    {
        $httpRequest = Mockery::mock(\Nette\Http\IRequest::class);
        $httpResponse = Mockery::mock(\Nette\Http\IResponse::class);

        ob_start();
        $this->response->send($httpRequest, $httpResponse);
        $content = ob_get_contents();
        ob_end_clean();

        Assert::equal($content, '');
        Assert::true(empty($content));
    }
}

(new NullResponseTest())->run();