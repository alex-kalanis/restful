<?php

namespace Tests\Picabo\Restful\Security\Process;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockery;
use Picabo\OAuth2\IKeyGenerator;
use Picabo\OAuth2\Storage\AccessTokens;
use Picabo\OAuth2\Storage\AccessTokens\IAccessToken;
use Picabo\Restful\Security;
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

    private $input;

    private $inputFake;

    public function testSuccessfullyAuthenticateAccessToken(): void
    {
        $token = '54a6f2ewq86f25rgr6n8r58hr28tj6vd';

        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn($token);

        $process = new Security\Process\OAuth2Authentication(
            new AccessTokens\AccessTokenFacade(
                100,
                new XKeyGenerator(),
                new XAccessTokenStorageEntityBase()
            ), $this->input);

        Assert::true($process->authenticate($this->inputFake));
    }

    public function testThrowsExceptionWhenTokenIsNotFoundOnInput(): void
    {
        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn(NULL);

        Assert::throws(function () {
            $process = new Security\Process\OAuth2Authentication(
                new AccessTokens\AccessTokenFacade(
                    100,
                    new XKeyGenerator(),
                    new XAccessTokenStorageEntityBase()
                ), $this->input);
            $process->authenticate($this->inputFake);
        }, Security\Exceptions\AuthenticationException::class);
    }

    public function testThrowsExceptionWhenTokenIsExpiredOrInvalid(): void
    {
        $token = '54a6f2ewq86f25rgr6n8r58hr28tj6vd';

        $this->input->expects('getAuthorization')
            ->once()
            ->andReturn($token);

        Assert::throws(function () {
            $process = new Security\Process\OAuth2Authentication(
                new AccessTokens\AccessTokenFacade(
                    100,
                    new XKeyGenerator(),
                    new XAccessTokenStorageEntityFail()
                ), $this->input);
            $process->authenticate($this->inputFake);
        }, Security\Exceptions\AuthenticationException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->input = Mockery::mock(\Picabo\OAuth2\Http\IInput::class);
        $this->inputFake = Mockery::mock(\Picabo\Restful\Http\IInput::class);
    }

}


class XKeyGenerator implements IKeyGenerator
{
    public function generate(int $length = 40): string
    {
        return 'mock key';
    }
}


class XAccessTokenStorageEntityBase implements AccessTokens\IAccessTokenStorage
{
    public function store(IAccessToken $accessToken): void
    {
    }

    public function getValidAccessToken(string $accessToken): ?IAccessToken
    {
        return new AccessTokens\AccessToken($accessToken, new \DateTime(), 1, null, []);
    }

    public function remove(string $token): void
    {
    }
}


class XAccessTokenStorageEntityFail extends XAccessTokenStorageEntityBase
{
    public function getValidAccessToken(string $accessToken): ?IAccessToken
    {
        // not found
        return null;
    }
}

(new OAuth2AuthenticationTest())->run();
