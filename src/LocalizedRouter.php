<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\CompiledRouteCollection;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class LocalizedRouter extends Router
{
    /**
     * Create a new Router instance.
     */
    public function __construct(Dispatcher $events, ?Container $container = null)
    {
        parent::__construct($events, $container);
    }

    public function newRoute($methods, $uri, $action)
    {
        return (new LocalizedRoute($methods, $uri, $action))
            ->setRouter($this)
            ->setContainer($this->container);
    }

    /**
     * Set the route collection instance.
     *
     * @param  \Illuminate\Routing\RouteCollection  $routes
     * @return void
     */
    public function setRoutes(RouteCollection|CompiledRouteCollection $routes)
    {
        foreach ($routes as $route) {
            $route->setRouter($this)->setContainer($this->container);
        }

        $this->routes = $routes;

        $this->container->instance('routes', $this->routes);
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
        if ($this->container->bound(LocalizedResourceRegistrar::class)) {
            $registrar = $this->container->make(LocalizedResourceRegistrar::class);
        } else {
            $registrar = new LocalizedResourceRegistrar($this);
        }

        return new PendingLocalizedRouteRegistration(
            $registrar, $name, $controller, $options
        );
    }
}
