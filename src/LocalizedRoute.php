<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use BackedEnum;
use Illuminate\Routing\Route;
use InvalidArgumentException;
use Illuminate\Support\Str;

class LocalizedRoute extends Route
{
    protected bool $isLocalized = false;

    protected bool $isProcessed = false;


    /**
     * The locale of the route.
     *
     * @var string
     */
    protected string $locale;

    /**
     * Get the locale of the route.
     *
     * @return string
     */
    public function getLocale() : string {
        return $this->locale;
    }

    /**
     * Set the locale of the route.
     *
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale) : self {
        $this->locale = $locale;
        return $this;
    }

    private function setLocaleWithUriAndName(string $locale) : self {
        $this->locale = $locale;
        // $locale == config('localized-routes-plus.default_locale') && config('localized-routes-plus.use_route_prefix_in_default_locale') == false
        if(!($locale == config('localized-routes-plus.default_locale') && config('localized-routes-plus.use_route_prefix_in_default_locale') == false)) {
            $this->uri = $this->locale.'/'.$this->uri;
        }
        if($this->getName()) {
            $this->action['as'] = $this->locale.'.'.$this->action['as'];
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
     * @param string $name
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
            throw new \Exception('Route already processed! Name must be set before localized() is called.');
        }

        return $this;
    }

    /**
     * Process the localization of the route.
     *
     * @return void
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
                $newRoute->setLocaleWithUriAndName($locale);
                $newRoute->setRouter($this->router)->setContainer($this->container);

                // Hozzáadjuk a route collection-höz
                $this->router->getRoutes()->add($newRoute);
            }
        }
    }


    /**
     * Get the uri of the route for a specific locale.
     *
     * @param string|null $locale If null, return the uri of the route for the current locale.
     * @return string
     */
    public function getRouteUri($locale = null) : string {
        if($locale) {
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
