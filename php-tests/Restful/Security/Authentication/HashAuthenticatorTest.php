<?php

namespace Tests\Restful\Security\Authentication;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Security\Authentication\HashAuthenticator;
use kalanis\Restful\Security\HashCalculator;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class HashAuthenticatorTest extends TestCase
{

    private $input;

    private $request;

    private $calculator;

    private HashAuthenticator $authenticator;

    public function testSuccessfulAuthentication(): void
    {
        $dataString = 'message=Testing+hash&sender=%40drahomir_hanak';

        $this->request->expects('getHeader')
            ->once()
            ->with(HashAuthenticator::AUTH_HEADER)
            ->andReturn(hash_hmac(HashCalculator::HASH, $dataString, 'topSecretKey'));

        $this->calculator->expects('calculate')
            ->once()
            ->with($this->input)
            ->andReturn(hash_hmac(HashCalculator::HASH, $dataString, 'topSecretKey'));

        $result = $this->authenticator->authenticate($this->input);
        Assert::true($result);
    }

    public function testWrongAuthenticationHash(): void
    {
        $dataString = 'message=Testing+hash&sender=%40drahomir_hanak';

        $this->request->expects('getHeader')
            ->once()
            ->with(HashAuthenticator::AUTH_HEADER)
            ->andReturn('totaly wrong hash');

        $this->calculator->expects('calculate')
            ->once()
            ->with($this->input)
            ->andReturn(hash_hmac(HashCalculator::HASH, $dataString, 'topSecretKey'));

        Assert::throws(function () {
            $this->authenticator->authenticate($this->input);
        }, \kalanis\Restful\Security\Exceptions\AuthenticationException::class);
    }

    public function testMissingAuthenticationHeader(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with(HashAuthenticator::AUTH_HEADER)
            ->andReturn(NULL);

        Assert::throws(function () {
            $this->authenticator->authenticate($this->input);
        }, \kalanis\Restful\Security\Exceptions\AuthenticationException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->input = Mockery::mock(\kalanis\Restful\Http\IInput::class);
        $this->calculator = Mockery::mock(\kalanis\Restful\Security\IAuthTokenCalculator::class);
        $this->request = Mockery::mock(\Nette\Http\IRequest::class);
        $this->authenticator = new HashAuthenticator($this->request, $this->calculator);
    }
}


(new HashAuthenticatorTest())->run();
