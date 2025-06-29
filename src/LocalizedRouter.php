<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class LocalizedRouter extends Router
{

    /**
     * Create a new Router instance.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @param  \Illuminate\Container\Container|null  $container
     */
    public function __construct(Dispatcher $events, ?Container $container = null)
    {
        parent::__construct($events, $container);
    }

}
