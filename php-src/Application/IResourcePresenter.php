<?php

namespace kalanis\Restful\Application;


use Nette\Application\IPresenter;


/**
 * REST API ResourcePresenter
 * @package kalanis\Restful\Application
 */
interface IResourcePresenter extends IPresenter
{

    /**
     * Set API resource
     */
    public function sendResource(): void;
}
