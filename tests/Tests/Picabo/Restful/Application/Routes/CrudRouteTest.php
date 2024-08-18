<?php

namespace Tests\Picabo\Restful\Application\Routes;

require_once __DIR__ . '/../../../../bootstrap.php';

use Picabo\Restful\Application\Routes\CrudRoute;
use Picabo\Restful\Application\IResourceRouter;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Application\Routes\CrudRoute.
 *
 * @testCase Tests\Picabo\Restful\Application\Routes\CrudRouteTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Application\Routes
 */
class CrudRouteTest extends TestCase
{

    /** @var CrudRoute */
    private $route;

    protected function setUp(): void
    {
        parent::setUp();
        $this->route = new CrudRoute('resources/crud', 'Crud');
    }

    public function testPredefinedCrudActionDictionary(): void
    {
        $array = $this->route->getActionDictionary();
        Assert::equal($array[IResourceRouter::POST], CrudRoute::ACTION_CREATE);
        Assert::equal($array[IResourceRouter::GET], CrudRoute::ACTION_READ);
        Assert::equal($array[IResourceRouter::PUT], CrudRoute::ACTION_UPDATE);
        Assert::equal($array[IResourceRouter::DELETE], CrudRoute::ACTION_DELETE);
    }
}

(new CrudRouteTest())->run();
