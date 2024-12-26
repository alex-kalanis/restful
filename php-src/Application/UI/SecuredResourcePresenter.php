<?php

namespace kalanis\Restful\Application\UI;


use kalanis\Restful\Security\Process\BasicAuthentication;


/**
 * SecuredResourcePresenter
 * @package kalanis\Restful\Application
 */
class SecuredResourcePresenter extends ResourcePresenter
{

    #[\Nette\DI\Attributes\Inject]
    public BasicAuthentication $basicAuthentication;

    /**
     * On presenter startup
     */
    protected function startup(): void
    {
        parent::startup();
        $this->authentication->setAuthProcess($this->basicAuthentication);
    }
}
