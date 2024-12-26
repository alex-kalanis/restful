<?php

namespace Tests\Restful\Application\Routes;

require_once dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'bootstrap.php';


use kalanis\Restful\Application\IResourceRouter;
use kalanis\Restful\Application\Routes\CrudRoute;
use Tester\Assert;
use Tests\TestCase;


class CrudRouteTest extends TestCase
{

    private CrudRoute $route;

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
