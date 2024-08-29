<?php

namespace Tests\Picabo\Restful\Security;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Security\HashCalculator;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Security\HashCalculator.
 *
 * @testCase Tests\Picabo\Restful\Security\HashCalculatorTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Security
 */
class HashCalculatorTest extends TestCase
{

    private $mapper;

    private $input;

    private HashCalculator $calculator;

    public function testCalculateHash(): void
    {
        $dataString = 'message=Testing+hash&sender=%40drahomir_hanak';
        $data = array('message' => 'Testing hash', 'sender' => '@drahomir_hanak');
        $this->mapper->expects('stringify')
            ->once()
            ->with($data, false)
            ->andReturn($dataString);

        $this->input->expects('getData')
            ->once()
            ->andReturn($data);

        $this->calculator->setPrivateKey('topSecretKey');
        $hash = $this->calculator->calculate($this->input);

        Assert::equal(hash_hmac(HashCalculator::HASH, $dataString, 'topSecretKey'), $hash);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = Mockery::mock(\Picabo\Restful\Mapping\QueryMapper::class, \Picabo\Restful\Mapping\IMapper::class);
        $this->input = Mockery::mock(\Picabo\Restful\Http\IInput::class);

        $request = Mockery::mock(\Nette\Http\IRequest::class);
        $request->expects('getHeader')->once()->with('content-type')->andReturn('text/plain');
        $mapperContext = Mockery::mock(\Picabo\Restful\Mapping\MapperContext::class);
        $mapperContext->expects('getMapper')->once()->with('text/plain')->andReturn($this->mapper);
        $this->calculator = new HashCalculator($mapperContext, $request);
    }

}

(new HashCalculatorTest())->run();
