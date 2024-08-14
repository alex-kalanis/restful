<?php

namespace Drahak\Restful\Application\UI;

use Drahak\Restful\Security\Process\BasicAuthentication;

/**
 * SecuredResourcePresenter
 * @package Drahak\Restful\Application
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
    protected function startup()
    {
        parent::startup();
        $this->authentication->setAuthProcess($this->basicAuthentication);
    }


}
