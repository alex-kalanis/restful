<?php

namespace Tests;


use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tester;


/**
 * TestCase
 * @package Tests
 */
class TestCase extends Tester\TestCase
{
    use MockeryPHPUnitIntegration;
}
