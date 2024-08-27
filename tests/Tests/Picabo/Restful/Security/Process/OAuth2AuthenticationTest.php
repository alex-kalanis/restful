<?php

namespace Tests\Picabo\Restful\Security\Process;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockery;
use Picabo\OAuth2\Storage\Exceptions\InvalidAccessTokenException;
use Picabo\Restful\Security\Process\OAuth2Authentication;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Security\Process\OAuth2Authentication.
 *
 * @testCase Tests\Picabo\Restful\Security\Process\OAuth2AuthenticationTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Security\Process
 */
class OAuth2AuthenticationTest extends TestCase
{

    private $token;

    private $input;

    private $inputFake;

    /** @var OAuth2Authentication */
    private $process;

    public function testSuccessfullyAuthenticateAccessToken(): void
    {
        $token = '54a6f2ewq86f25rgr6n8r58hr28tj6vd';

        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn($token);

        $this->token->expects('getEntity')
            ->once()
            ->with($token)
            ->andReturn(array('access_token' => $token));

        Assert::true($this->process->authenticate($this->inputFake));
    }

    public function testThrowsExceptionWhenTokenIsNotFoundOnInput(): void
    {
        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn(NULL);

        Assert::throws(function () {
            $this->process->authenticate($this->inputFake);
        }, \Picabo\Restful\Security\Exceptions\AuthenticationException::class);
    }

    public function testThrowsExceptionWhenTokenIsExpiredOrInvalid(): void
    {
        $token = '54a6f2ewq86f25rgr6n8r58hr28tj6vd';
        $invalidTokenException = new InvalidAccessTokenException;

        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn($token);

        $this->token->expects('getEntity')
            ->once()
            ->with($token)
            ->andReturn(NULL)
            ->andThrow($invalidTokenException);

        Assert::throws(function () {
            $this->process->authenticate($this->inputFake);
        }, \Picabo\Restful\Security\Exceptions\AuthenticationException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->token = Mockery::mock(\Picabo\OAuth2\Storage\AccessTokens\AccessTokenFacade::class, \Picabo\OAuth2\Storage\ITokenFacade::class);
        $this->input = Mockery::mock(\Picabo\OAuth2\Http\IInput::class);
        $this->inputFake = Mockery::mock(\Picabo\Restful\Http\IInput::class);
        $this->process = new OAuth2Authentication($this->token, $this->input);
    }

}

(new OAuth2AuthenticationTest())->run();
