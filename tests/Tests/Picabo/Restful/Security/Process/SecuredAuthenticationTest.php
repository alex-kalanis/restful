<?php

namespace Tests\Picabo\Restful\Security\Process;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockista\MockInterface;
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

    /** @var MockInterface */
    private $input;

    /** @var MockInterface */
    private $hashAuth;

    /** @var MockInterface */
    private $timeAuth;

    /** @var SecuredAuthentication */
    private $process;

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
        $this->input = $this->mockista->create(\Picabo\Restful\Http\IInput::class);
        $this->hashAuth = $this->mockista->create(\Picabo\Restful\Security\Authentication\HashAuthenticator::class);
        $this->timeAuth = $this->mockista->create(\Picabo\Restful\Security\Authentication\TimeoutAuthenticator::class);
        $this->process = new SecuredAuthentication($this->hashAuth, $this->timeAuth);
    }

}

(new SecuredAuthenticationTest())->run();
