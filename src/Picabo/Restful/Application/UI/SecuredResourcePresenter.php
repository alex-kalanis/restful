<?php

namespace Picabo\Restful\Application\UI;

use Picabo\Restful\Security\Process\BasicAuthentication;

/**
 * SecuredResourcePresenter
 * @package Picabo\Restful\Application
 * @author Drahomír Hanák
 */
class SecuredResourcePresenter extends ResourcePresenter
{

    /** @var BasicAuthentication */
    private $basicAuthentication;

    public final function injectBasicAuthentication(BasicAuthentication $basicAuthentication)
    {
        $this->basicAuthentication = $basicAuthentication;
    }

    /**
     * On presenter startup
     */
    protected function startup(): void
    {
        parent::startup();
        $this->authentication->setAuthProcess($this->basicAuthentication);
    }
}
