<?php

namespace Tests\Restful\Security;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Mapping\QueryMapper;
use kalanis\Restful\Security\HashCalculator;
use Mockery;
use Tester\Assert;
use Tests\TestCase;


class HashCalculatorTest extends TestCase
{

    private QueryMapper $mapper;

    private $input;

    private HashCalculator $calculator;

    public function testCalculateHash(): void
    {
        $dataString = 'message=Testing+hash&sender=%40drahomir_hanak';
        $data = ['message' => 'Testing hash', 'sender' => '@drahomir_hanak'];

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
        $this->input = Mockery::mock(\kalanis\Restful\Http\IInput::class);

        $request = Mockery::mock(\Nette\Http\IRequest::class);
        $request->expects('getHeader')->once()->with('content-type')->andReturn('text/plain');
        $mapperContext = Mockery::mock(\kalanis\Restful\Mapping\MapperContext::class);
        $mapperContext->expects('getMapper')->once()->with('text/plain')->andReturn($this->mapper);
        $this->calculator = new HashCalculator($mapperContext, $request);
    }
}


(new HashCalculatorTest())->run();
