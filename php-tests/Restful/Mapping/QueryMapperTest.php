<?php

namespace Tests\Restful\Mapping;

require_once dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Mapping\QueryMapper;
use Tester\Assert;
use Tests\TestCase;


class QueryMapperTest extends TestCase
{

    private QueryMapper $mapper;

    public function testParseRequest(): void
    {
        $query = 'message=Follow+me+on+Twitter&sender=%40drahomir_hanak';
        $data = $this->mapper->parse($query);
        Assert::equal($data['message'], 'Follow me on Twitter');
        Assert::equal($data['sender'], '@drahomir_hanak');
    }

    public function testParseResponse(): void
    {
        $data['message'] = 'Follow me on Twitter';
        $data['sender'] = '@drahomir_hanak';
        $data['specialChars'] = '+_-!@*()';
        $query = $this->mapper->stringify($data);
        Assert::equal($query, 'message=Follow+me+on+Twitter&sender=%40drahomir_hanak&specialChars=%2B_-%21%40%2A%28%29');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new QueryMapper;
    }
}


(new QueryMapperTest())->run();
