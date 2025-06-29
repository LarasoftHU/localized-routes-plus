<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Route;

class PendingLocalizedRouteRegistration extends PendingResourceRegistration
{
    /**
     * If true the localized functions will be called.
     */
    protected bool $mustBeLocalized = false;

    /**
     * The locales to be localized.
     */
    protected array $locales = [];

    /**
     * If true, the URI is already localized.
     * Used for resource auto registering prefixes
     */
    protected bool $uriLocalized = false;

    /**
     * Localize the routes for the given locales. If no locales are given, all locales will be localized.
     */
    public function localized(array|string $locales = []): self
    {
        $this->mustBeLocalized = true;
        if (is_string($locales)) {
            $locales = [$locales];
        }
        $this->locales = $locales;

        return $this;
    }

    /**
     *  Localize all routes except the given locales. If no locales are given, all locales will be localized.
     */
    public function localizedExcept(array|string $locales = []): self
    {
        $this->mustBeLocalized = true;
        if (is_string($locales)) {
            $locales = [$locales];
        }
        $this->locales = array_diff(config('localized-routes-plus.locales'), $locales);

        return $this;
    }

    /**
     * Create a new pending resource registration instance.
     *
     * @param  string  $name
     * @param  string  $controller
     */
    public function __construct(ResourceRegistrar $registrar, $name, $controller, array $options)
    {
        parent::__construct($registrar, $name, $controller, $options);
    }

    /**
     * Register the resource route.
     *
     * @return \Illuminate\Routing\RouteCollection
     */
    public function register()
    {
        $this->registered = true;

        $locales = [];
        if ($this->mustBeLocalized) {
            if (empty($this->locales) || count($this->locales) == 0) {
                $locales = config('localized-routes-plus.locales');
            } else {
                $locales = $this->locales;
            }
        }

        return $this->registrar->register(
            $this->name, $this->controller, $this->options, $locales
        );
    }
}
