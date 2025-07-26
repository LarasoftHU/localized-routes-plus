<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LarasoftHU\LocalizedRoutesPlus\Middleware\SetCountryFromRoute;
use LarasoftHU\LocalizedRoutesPlus\Middleware\SetLocaleFromRoute;

class LocalizedRoute extends Route
{
    protected bool $isLocalized = false;

    protected bool $isProcessed = false;

    /**
     * The locale of the route.
     */
    protected ?string $locale = null;

    /**
     * The country of the route.
     */
    protected ?string $country = null;

    /**
     * Create a new Route instance.
     *
     * @param  array|string  $methods
     * @param  string  $uri
     * @param  \Closure|array  $action
     */
    public function __construct($methods, $uri, $action)
    {
        parent::__construct($methods, $uri, $action);
        $this->isLocalized = false;
        $this->isProcessed = false;
    }

    /**
     * Get the locale of the route.
     */
    public function getLocale(): ?string
    {
        if ($this->locale) {
            return $this->locale;
        }

        if ($this->getName()) {
            if (config('localized-routes-plus.use_countries')) {
                return explode('-', explode('.', $this->getName())[0])[0];
            }

            return explode('.', $this->getName())[0];
        }

        return null;
    }

    /**
     * Set the locale of the route.
     *
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the country of the route.
     */
    public function getCountry(): ?string
    {
        if ($this->country) {
            return $this->country;
        }

        if ($this->getName()) {
            if (config('localized-routes-plus.use_countries')) {
                return explode('-', explode('.', $this->getName())[0])[1];
            }
        }

        return null;
    }

    /**
     * Set the country of the route.
     *
     * @return $this
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Mark the route as localized.
     *
     * @return $this
     */
    public function localized(array|string $locales = []): self
    {
        $this->isLocalized = true;

        $this->middleware(SetLocaleFromRoute::class);
        if (config('localized-routes-plus.use_countries')) {
            $this->middleware(SetCountryFromRoute::class);
        }
        $this->processLocalization(Arr::wrap($locales));

        return $this;
    }

    /**
     *  Localize all routes except the given locales. If no locales are given, all locales will be localized.
     */
    public function localizedExcept(array|string $locales = []): self
    {
        $locales = array_diff(config('localized-routes-plus.locales'), Arr::wrap($locales));

        $this->localized($locales);

        return $this;
    }

    /**
     * Set the name of the route.
     *
     * @param  string  $name
     * @return $this
     */
    public function name($name): self
    {
        if ($this->isProcessed) {
            throw new InvalidArgumentException('Route already processed! Name must be set before localized() is called.');
        }

        return parent::name($name);
    }

    /**
     * Process the localization of the route.
     */
    protected function processLocalization(array $locales = []): void
    {
        $this->isProcessed = true;

        if (count($locales) == 0) {
            $locales = config('localized-routes-plus.locales', ['en']);
        }

        $defaultLocale = config('localized-routes-plus.default_locale', 'en');

        if (! in_array($defaultLocale, $locales)) {
            $defaultLocale = $locales[0];
        }

        $original = clone $this;

        // Létrehozzuk a többi locale-hoz is a route-okat
        foreach ($locales as $locale) {
            if ($locale !== $defaultLocale) {
                // Új route regisztrálása - használjuk a normál Route osztályt, ne a LocalizedRoute-ot
                $newRoute = clone $original;
                $newRoute->setLocaleWithUriAndName($locale);
                // Hozzáadjuk a route collection-höz
                $this->router->getRoutes()->add($newRoute);
            }
        }

        // Az eredeti route-ot csak akkor állítjuk be a default locale-ra, ha az szerepel a megadott locale-ok között
        $this->setLocaleWithUriAndName($defaultLocale);

        $this->router->getRoutes()->refreshNameLookups();
        $this->router->getRoutes()->refreshActionLookups();

        // Ha az eredeti URI '/' és a use_route_prefix_in_default_locale true, akkor redirect route-ot hozunk létre
        if ($original->uri == '/' && config('localized-routes-plus.use_route_prefix_in_default_locale')) {
            // Meghatározzuk a redirect célpontját
            $redirectTarget = '/';

            if (config('localized-routes-plus.use_countries')) {
                $country = config('localized-routes-plus.countries')[$defaultLocale];
                if (is_array($country)) {
                    $country = $country[0];
                }

                $separator = config('localized-routes-plus.country_path_separator') == 'dash' ? '-' : '/';
                $redirectTarget = '/'.$defaultLocale.$separator.$country;
            } else {
                $redirectTarget = '/'.$defaultLocale;
            }

            // Létrehozzuk a redirect route-ot
            $this->router->redirect('/', $redirectTarget);
        }

        $this->router->getRoutes()->refreshNameLookups();
        $this->router->getRoutes()->refreshActionLookups();
    }

    /**
     * Set the locale of the route and update the uri and name of the route.
     *
     * @return $this
     */
    private function setLocaleWithUriAndName(string $locale): self
    {
        $this->setLocale($locale);

        if ($this->getName()) {
            if (config('localized-routes-plus.use_countries')) {
                if (is_array(config('localized-routes-plus.countries')[$locale])) {
                    $country = config('localized-routes-plus.countries')[$locale][0];
                } else {
                    $country = config('localized-routes-plus.countries')[$locale];
                }
                $this->action['as'] = $this->getLocale().'-'.$country.'.'.$this->action['as'];
                $this->setCountry($country);
            } else {
                $this->action['as'] = $this->getLocale().'.'.$this->action['as'];
            }
        }

        // Subdomains are handled here
        if (config('localized-routes-plus.use_subdomains_instead_of_prefixes')) {
            if (isset(config('localized-routes-plus.domains')[$locale])) {
                if (is_array(config('localized-routes-plus.domains')[$locale])) {
                    if (count(config('localized-routes-plus.domains')[$locale]) > 0) {

                        // dd(config('localized-routes-plus.domains')[$locale][0]);
                        $originalName = $this->action['as'];
                        $domains = config('localized-routes-plus.domains')[$locale];
                        $this->domain($domains[0]);
                        $this->action['as'] = explode('.', $domains[0])[0].'-'.$originalName;
                        for ($i = 1, $count = count($domains); $i < $count; $i++) {
                            $copy = clone $this;
                            $copy->domain($domains[$i]);
                            if ($copy->getName()) {
                                $copy->action['as'] = explode('.', $copy->action['as'])[0].'-'.$originalName;
                            }
                            $this->router->getRoutes()->add($copy);
                        }
                    }
                } else {
                    $this->domain(config('localized-routes-plus.domains')[$locale]);
                }
            } else {
                throw new InvalidArgumentException('Domain not found for locale: '.$locale.' If you want to exclude this locale, you can use the localizedExcept(\''.$locale.'\') method or remove from locales.');
            }
        } else {
            // Set prefix
            if (
                ! ($locale == config('localized-routes-plus.default_locale') && config('localized-routes-plus.use_route_prefix_in_default_locale') == false)
            ) {
                // Fix: Properly handle root URI ('/') to avoid double slashes

                $prefix = $this->getLocale();

                if (config('localized-routes-plus.use_countries')) {
                    $separator = config('localized-routes-plus.country_path_separator') == 'dash' ? '-' : '/';
                    $countryForLocale = config('localized-routes-plus.countries')[$locale];

                    if (is_array($countryForLocale)) {
                        $prefix = $prefix.$separator.$countryForLocale[0];

                        for ($i = 1, $count = count($countryForLocale); $i < $count; $i++) {
                            $copy = clone $this;

                            $_prefix = $locale.$separator.$countryForLocale[$i];
                            $copy->localizeUri();
                            $copy->prefix($_prefix);

                            if ($copy->getName()) {
                                $copy->action['as'] = str_replace('/', '-', $_prefix).'.'.$copy->getSafeName();
                            }
                            $copy->setCountry($countryForLocale[$i]);

                            $this->router->getRoutes()->add($copy);
                        }
                    } else {
                        $prefix = $prefix.$separator.$countryForLocale;
                    }
                }

                $this->localizeUri();
                $this->prefix($prefix);
            } else {
                $this->localizeUri();
            }
        }

        return $this;
    }

    private function localizeUri(): self
    {
        if (Lang::has('routes.'.$this->getSafeName(), $this->getLocale(), false)) {
            $localizedUri = Lang::get('routes.'.$this->getSafeName(), [], $this->getLocale(), false);
            $this->setUri($localizedUri);

            return $this;
        }

        return $this;
    }

    /**
     * Get the uri of the route for a specific locale.
     *
     * @param  string|null  $locale  If null, return the uri of the route for the current locale.
     */
    public function locale($locale = null, $country = null): ?LocalizedRoute
    {
        if (! $this->getLocale()) {
            throw new InvalidArgumentException('Route is not localized so you can not use locale() method!');
        }

        if ($country && ! config('localized-routes-plus.use_countries')) {
            throw new InvalidArgumentException('You can not use country parameter without use_countries config!');
        }

        if (! $country && config('localized-routes-plus.use_countries')) {
            throw new InvalidArgumentException('You can not use locale() method without country parameter if use_countries config is true!');
        }

        if ($locale && ! $country) {
            $route = $this->router->getRoutes()->getByName($locale.'.'.$this->getSafeName());

            return $route instanceof LocalizedRoute ? $route : null;
        }

        if ($locale && $country) {
            $route = $this->router->getRoutes()->getByName($locale.'-'.$country.'.'.$this->getSafeName());

            return $route instanceof LocalizedRoute ? $route : null;
        }

        return $this;
    }

    /**
     * Check if the route is a specific name without the locale.
     *
     * @param  string  $name
     */
    public function is($name): bool
    {
        if ($this->getLocale()) {
            return $this->getSafeName() == $name;
        }

        return $this->action['as'] == $name;
    }

    /**
     * Get the safe name of the route without the locale.
     */
    public function getSafeName(): ?string
    {
        if (! $this->getName()) {
            return null;
        }
        if (! config('localized-routes-plus.use_countries')) {
            return Str::replaceFirst(
                $this->getLocale().'.',
                '',
                $this->action['as']
            );
        } else {
            return Str::replaceFirst(
                $this->getLocale().'-'.$this->getCountry().'.',
                '',
                $this->action['as']
            );
        }
    }

    /**
     * Get the url of the route for a specific locale.
     */
    public function getUrl($locale = null, $country = null, $parameters = null, bool $absolute = true): string
    {
        if ($locale && ! $country && config('localized-routes-plus.use_countries')) {
            throw new InvalidArgumentException('You can not use getUrl() method without country parameter if use_countries config is true!');
        }

        if ($country && ! config('localized-routes-plus.use_countries')) {
            throw new InvalidArgumentException('You can not use getUrl() method with country parameter if use_countries config is false!');
        }

        if (! $parameters) {
            $parameters = [];
            if (isset($this->parameters)) {
                $parameters = $this->parameters();
            }
        }

        if ($locale && ! $country) {
            return route($locale.'.'.$this->getSafeName(), $parameters, $absolute);
        }

        if ($locale && $country) {
            return route($locale.'-'.$country.'.'.$this->getSafeName(), $parameters, $absolute);
        }

        return route($this->getSafeName(), $parameters, $absolute);
    }
}
