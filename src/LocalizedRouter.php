<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\Router;

class LocalizedRouter extends Router
{
    public function newRoute($methods, $uri, $action)
    {
        return (new LocalizedRoute($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @return \LarasoftHU\LocalizedRoutesPlus\PendingLocalizedRouteRegistration
     */
    public function resource($name, $controller, array $options = [])
    {
        if ($this->container && $this->container->bound(LocalizedResourceRegistrar::class)) {
            $registrar = $this->container->make(LocalizedResourceRegistrar::class);
        } else {
            $registrar = new LocalizedResourceRegistrar($this);
        }

        return new PendingLocalizedRouteRegistration(
            $registrar, $name, $controller, $options
        );
    }
}
