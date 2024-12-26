<?php

namespace Tests\Restful\Security\Authentication;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Security\Authentication\TimeoutAuthenticator;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class TimeoutAuthenticatorTest extends TestCase
{

    private $input;

    private TimeoutAuthenticator $authenticator;

    public function testSuccessfulAuthentication(): void
    {
        $data = ['timestamp' => time()];
        $this->input->expects('getData')
            ->once()
            ->andReturn($data);

        $result = $this->authenticator->authenticate($this->input);
        Assert::true($result);
    }

    public function testRequestTimeoutException(): void
    {
        $data = ['timestamp' => time() - 601];
        $this->input->expects('getData')
            ->once()
            ->andReturn($data);

        Assert::throws(function () {
            $this->authenticator->authenticate($this->input);
        }, \kalanis\Restful\Security\Exceptions\RequestTimeoutException::class);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->input = Mockery::mock(\kalanis\Restful\Http\IInput::class);
        $this->authenticator = new TimeoutAuthenticator('timestamp', 600);
    }
}


(new TimeoutAuthenticatorTest())->run();
