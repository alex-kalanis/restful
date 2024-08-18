<?php

namespace Tests\Picabo\Restful\Application\Responses;

require_once __DIR__ . '/../../../../bootstrap.php';

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
    /** @var NullResponse */
    private $response;

    protected function setUp(): void
    {
        parent::setUp();
        $this->response = new NullResponse;
    }

    public function testDoNotSendResponse(): void
    {
        $httpRequest = $this->mockista->create(\Nette\Http\IRequest::class);
        $httpResponse = $this->mockista->create(\Nette\Http\IResponse::class);

        ob_start();
        $result = $this->response->send($httpRequest, $httpResponse);
        $content = ob_get_contents();
        ob_end_clean();

        Assert::equal($content, '');
        Assert::null($result);
    }
}

(new NullResponseTest())->run();