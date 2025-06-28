<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Route;

class PendingLocalizedRouteRegistration extends PendingResourceRegistration
{
    protected bool $mustBeLocalized = false;

    public function localized(): self
    {
        $this->mustBeLocalized = true;

        return $this;
    }

    /**
     * Register the resource route.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register()
    {
        $this->registered = true;

        return $this->registrar->registerLocalized(
            $this->name, $this->controller, $this->options
        );
    }
}
