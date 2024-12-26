<?php

namespace Tests\Restful\Application\Responses;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\Responses\NullResponse;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


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