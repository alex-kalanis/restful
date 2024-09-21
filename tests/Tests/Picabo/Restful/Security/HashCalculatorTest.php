<?php

namespace Tests\Picabo\Restful\Security;

require_once __DIR__ . '/../../../bootstrap.php';

use Mockery;
use Picabo\Restful\Mapping\QueryMapper;
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

    private QueryMapper $mapper;

    private $input;

    private HashCalculator $calculator;

    public function testCalculateHash(): void
    {
        $dataString = 'message=Testing+hash&sender=%40drahomir_hanak';
        $data = array('message' => 'Testing hash', 'sender' => '@drahomir_hanak');

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
        $this->mapper = new QueryMapper();
        $this->input = Mockery::mock(\Picabo\Restful\Http\IInput::class);

        $request = Mockery::mock(\Nette\Http\IRequest::class);
        $request->expects('getHeader')->once()->with('content-type')->andReturn('text/plain');
        $mapperContext = Mockery::mock(\Picabo\Restful\Mapping\MapperContext::class);
        $mapperContext->expects('getMapper')->once()->with('text/plain')->andReturn($this->mapper);
        $this->calculator = new HashCalculator($mapperContext, $request);
    }

}

(new HashCalculatorTest())->run();
