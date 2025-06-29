<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use BackedEnum;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteUri;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LocalizedRoute extends Route
{
    protected bool $isLocalized = false;

    protected bool $isProcessed = false;

    public function __construct($methods, $uri, $action)
    {
        parent::__construct($methods, $uri, $action);
        $this->isLocalized = false;
        $this->isProcessed = false;
    }

    /**
     * The locale of the route.
     */
    protected ?string $locale = null;

    /**
     * Get the locale of the route.
     */
    public function getLocale(): ?string
    {
        return $this->locale ?? null;
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
     * Set the locale of the route and update the uri and name of the route.
     *
     * @param  string  $locale
     * @return $this
     */
    private function setLocaleWithUriAndName(string $locale): self
    {
        $this->locale = $locale;
        // $locale == config('localized-routes-plus.default_locale') && config('localized-routes-plus.use_route_prefix_in_default_locale') == false
        
        
        if ($this->getName()) {
            $this->action['as'] = $this->locale.'.'.$this->action['as'];
        }

        // Subdomains are handled here
        if(config('localized-routes-plus.use_subdomains_instead_of_prefixes')){
            if(isset(config('localized-routes-plus.domains')[$locale])){
                if(is_array(config('localized-routes-plus.domains')[$locale])){
                    if(count(config('localized-routes-plus.domains')[$locale]) > 0){
                        
                        //dd(config('localized-routes-plus.domains')[$locale][0]);
                        $originalName = $this->action['as'];
                        $domains = config('localized-routes-plus.domains')[$locale];
                        $this->domain($domains[0]);
                        $this->action['as'] = explode('.', $domains[0])[0].'-'.$originalName;
                        for ($i = 1, $count = count($domains); $i < $count; $i++) {

                            $copy = clone $this;
                            $copy->domain($domains[$i]);
                            $copy->action['as'] = explode('.', $domains[$i])[0].'-'.$originalName;
                            $this->router->getRoutes()->add($copy);
                        }
                    }
                }
                else {
                    $this->domain(config('localized-routes-plus.domains')[$locale]);
                }
            }
            else {
                throw new InvalidArgumentException('Domain not found for locale: '.$locale. ' If you want to exclude this locale, you can use the localizedExcept(\''.$locale.'\') method or remove from locales.');
            }
        }
        else {
            // Set prefix
            if (
                ! ($locale == config('localized-routes-plus.default_locale') && config('localized-routes-plus.use_route_prefix_in_default_locale') == false)
            ) {
                // Fix: Properly handle root URI ('/') to avoid double slashes
                $this->uri = rtrim($this->locale.'/'.ltrim($this->uri, '/'), '/');
            }
        }


        return $this;
    }

    /**
     * Mark the route as localized.
     *
     * @return $this
     */
    public function localized(): self
    {
        $this->isLocalized = true;

        // Ha már van név beállítva, rögtön feldolgozzuk
        $this->processLocalization();

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
        // Laravel kompatibilis name() metódus implementáció
        if ($name instanceof BackedEnum && ! is_string($name = $name->value)) {
            throw new InvalidArgumentException('Enum must be string backed.');
        }

        $this->action['as'] = isset($this->action['as']) ? $this->action['as'].$name : $name;

        // Ha localized és még nem dolgoztuk fel, feldolgozzuk
        if ($this->isProcessed) {
            throw new InvalidArgumentException('Route already processed! Name must be set before localized() is called.');
        }

        return $this;
    }

    /**
     * Process the localization of the route.
     */
    protected function processLocalization(): void
    {
        $this->isProcessed = true;

        $originalName = $this->action['as'] ?? null;

        $locales = config('localized-routes-plus.locales', ['en']);
        $defaultLocale = config('localized-routes-plus.default_locale', 'en');

        $original = clone $this;

        // Az eredeti route-ot átnevezzük a default locale-lal
        $this->setLocaleWithUriAndName($defaultLocale);

        // KRITIKUS: Frissítjük a RouteCollection name lookup cache-t
        $this->router->getRoutes()->refreshNameLookups();

        // Létrehozzuk a többi locale-hoz is a route-okat
        foreach ($locales as $locale) {
            if ($locale !== $defaultLocale) {
                $newAction = $original->action;
                $newAction['as'] = $originalName;

                // Új route regisztrálása - használjuk a normál Route osztályt, ne a LocalizedRoute-ot
                $newRoute = new LocalizedRoute($original->methods(), $original->uri(), $newAction);
                $newRoute->setRouter($this->router)->setContainer($this->container);
                
                // Hozzáadjuk a route collection-höz
                $newRoute->setLocaleWithUriAndName($locale);
                $this->router->getRoutes()->add($newRoute);
            }
        }
        $this->router->getRoutes()->refreshNameLookups();
        $this->router->getRoutes()->refreshActionLookups();
    }

    /**
     * Get the uri of the route for a specific locale.
     *
     * @param  string|null  $locale  If null, return the uri of the route for the current locale.
     */
    public function getRouteUri($locale = null): string
    {
        if ($locale) {
            $name = $this->action['as'];
            $safeName = Str::replaceFirst(
                $this->locale.'.',
                '',
                $name
            );

            return $this->router->getRoutes()->getByName($locale.'.'.$safeName)->uri;
        }

        return $this->uri;
    }
}
