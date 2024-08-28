<?php

namespace Tests\Picabo\Restful\Security\Process;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Security\Authentication;
use Picabo\Restful\Security\Process\SecuredAuthentication;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Security\Process\Process\SecuredAuthentication.
 *
 * @testCase Tests\Picabo\Restful\Security\Process\Process\SecuredAuthenticationTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Security\Process
 */
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
            ->andReturn(TRUE);

        $this->timeAuth->expects('authenticate')
            ->once()
            ->with($this->input)
            ->andReturn(TRUE);

        Assert::true($this->process->authenticate($this->input));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->input = Mockery::mock(\Picabo\Restful\Http\IInput::class);
        $this->hashAuth = Mockery::mock(Authentication\HashAuthenticator::class, Authentication\IRequestAuthenticator::class);
        $this->timeAuth = Mockery::mock(Authentication\TimeoutAuthenticator::class, Authentication\IRequestAuthenticator::class);
        $this->process = new SecuredAuthentication($this->hashAuth, $this->timeAuth);
    }

}

(new SecuredAuthenticationTest())->run();
