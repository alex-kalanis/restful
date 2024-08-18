<?php

namespace Picabo\Restful\Application;

use Nette\Application\IPresenter;

/**
 * REST API ResourcePresenter
 * @package Picabo\Restful
 * @author Drahomír Hanák
 */
interface IResourcePresenter extends IPresenter
{

    /**
     * Set API resource
     */
    public function sendResource(): void;
}
