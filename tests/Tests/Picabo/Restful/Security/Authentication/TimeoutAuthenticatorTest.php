<?php

namespace Tests\Picabo\Restful\Security\Authentication;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Security\Authentication\TimeoutAuthenticator;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Security\Authentication\TimeoutAuthenticator.
 *
 * @testCase Tests\Picabo\Restful\Security\Authentication\TimeoutAuthenticatorTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Security\Authentication
 */
class TimeoutAuthenticatorTest extends TestCase
{

    private $input;

    private TimeoutAuthenticator $authenticator;

    public function testSuccessfulAuthenticaton(): void
    {
        $data = array('timestamp' => time());
        $this->input->expects('getData')
            ->once()
            ->andReturn($data);

        $result = $this->authenticator->authenticate($this->input);
        Assert::true($result);
    }

    public function testRequestTimeoutException(): void
    {
        $data = array('timestamp' => time() - 601);
        $this->input->expects('getData')
            ->once()
            ->andReturn($data);

        Assert::throws(function () {
            $this->authenticator->authenticate($this->input);
        }, \Picabo\Restful\Security\Exceptions\RequestTimeoutException::class);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->input = Mockery::mock(\Picabo\Restful\Http\IInput::class);
        $this->authenticator = new TimeoutAuthenticator('timestamp', 600);
    }

}

(new TimeoutAuthenticatorTest())->run();
