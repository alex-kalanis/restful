<?php
namespace Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockista\Registry;
use Tester;

/**
 * TestCase
 * @package Tests
 * @author Drahomír Hanák
 */
class TestCase extends Tester\TestCase
{
    use MockeryPHPUnitIntegration;

    /** @var Registry */
    protected $mockista;

    protected function setUp()
    {
        $this->mockista = new Registry;
    }
}
