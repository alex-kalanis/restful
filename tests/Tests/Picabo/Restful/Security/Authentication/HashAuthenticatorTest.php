<?php

namespace Tests\Picabo\Restful\Security\Authentication;

require_once __DIR__ . '/../../../../bootstrap.php';

use Mockista\MockInterface;
use Picabo\Restful\Security\Authentication\HashAuthenticator;
use Picabo\Restful\Security\HashCalculator;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Security\Authentication\HashAuthenticator.
 *
 * @testCase Tests\Picabo\Restful\Security\Authentication\HashAuthenticatorTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Security\Authentication
 */
class HashAuthenticatorTest extends TestCase
{

    /** @var MockInterface */
    private $input;

    /** @var MockInterface */
    private $request;

    /** @var MockInterface */
    private $calculator;

    /** @var HashAuthenticator */
    private $authenticator;

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
        }, \Picabo\Restful\Security\Exceptions\AuthenticationException::class);
    }

    public function testMissingAuthenticationHeader(): void
    {
        $this->request->expects('getHeader')
            ->once()
            ->with(HashAuthenticator::AUTH_HEADER)
            ->andReturn(NULL);

        Assert::throws(function () {
            $this->authenticator->authenticate($this->input);
        }, \Picabo\Restful\Security\Exceptions\AuthenticationException::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->input = $this->mockista->create(\Picabo\Restful\Http\IInput::class);
        $this->calculator = $this->mockista->create(\Picabo\Restful\Security\IAuthTokenCalculator::class);
        $this->request = $this->mockista->create(\Nette\Http\IRequest::class);
        $this->authenticator = new HashAuthenticator('topSecretKey', $this->request, $this->calculator);
    }

}

(new HashAuthenticatorTest())->run();