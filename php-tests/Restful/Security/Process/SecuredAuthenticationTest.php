<?php

namespace Tests\Restful\Security\Process;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Security\Authentication;
use kalanis\Restful\Security\Process\SecuredAuthentication;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class SecuredAuthenticationTest extends TestCase
{

    private $input;

    private $hashAuth;

    private $timeAuth;

    private SecuredAuthentication $process;

    public function testAuthenticateRequest(): void
    {
        $this->hashAuth->expects('authenticate')
            ->once()
            ->with($this->input)
            ->andReturn(true);

        $this->timeAuth->expects('authenticate')
            ->once()
            ->with($this->input)
            ->andReturn(true);

        Assert::true($this->process->authenticate($this->input));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->input = Mockery::mock(\kalanis\Restful\Http\IInput::class);
        $this->hashAuth = Mockery::mock(Authentication\HashAuthenticator::class, Authentication\IRequestAuthenticator::class);
        $this->timeAuth = Mockery::mock(Authentication\TimeoutAuthenticator::class, Authentication\IRequestAuthenticator::class);
        $this->process = new SecuredAuthentication($this->hashAuth, $this->timeAuth);
    }
}


(new SecuredAuthenticationTest())->run();
