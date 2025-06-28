<?php

namespace LarasoftHU\LocalizedRoutesPlus;

use BackedEnum;
use Illuminate\Routing\Route;
use InvalidArgumentException;

class LocalizedRoute extends Route
{
    protected bool $isLocalized = false;

    protected bool $isProcessed = false;

    public function localized(): self
    {
        $this->isLocalized = true;

        // Ha már van név beállítva, rögtön feldolgozzuk
        if ($this->action['as'] ?? null) {
            $this->processLocalization();
        }

        return $this;
    }

    public function name($name): self
    {
        // Laravel kompatibilis name() metódus implementáció
        if ($name instanceof BackedEnum && ! is_string($name = $name->value)) {
            throw new InvalidArgumentException('Enum must be string backed.');
        }

        $this->action['as'] = isset($this->action['as']) ? $this->action['as'].$name : $name;

        // Ha localized és még nem dolgoztuk fel, feldolgozzuk
        if ($this->isLocalized && ! $this->isProcessed) {
            $this->processLocalization();
        }

        return $this;
    }

    protected function processLocalization(): void
    {
        if ($this->isProcessed) {
            return;
        }

        $originalName = $this->action['as'] ?? null;
        if (! $originalName) {
            return;
        }

        // Ellenőrizzük, hogy már feldolgoztuk-e (ne legyen duplikáció)
        if (str_contains($originalName, '.')) {
            return;
        }

        $locales = config('localized-routes-plus.locales', ['en']);
        $defaultLocale = config('localized-routes-plus.default_locale', 'en');

        // Az eredeti route-ot átnevezzük a default locale-lal
        $this->action['as'] = $defaultLocale.'.'.$originalName;

        // KRITIKUS: Frissítjük a RouteCollection name lookup cache-t
        $this->router->getRoutes()->refreshNameLookups();

        // Létrehozzuk a többi locale-hoz is a route-okat
        foreach ($locales as $locale) {
            if ($locale !== $defaultLocale) {
                $newAction = $this->action;
                $newAction['as'] = $locale.'.'.$originalName;

                // Új route regisztrálása - használjuk a normál Route osztályt, ne a LocalizedRoute-ot
                $newRoute = new Route($this->methods(), $this->uri(), $newAction);
                $newRoute->setRouter($this->router)->setContainer($this->container);

                // Hozzáadjuk a route collection-höz
                $this->router->getRoutes()->add($newRoute);
            }
        }

        $this->isProcessed = true;
    }
}
