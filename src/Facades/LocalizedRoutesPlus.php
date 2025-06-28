<?php

namespace LarasoftHU\LocalizedRoutesPlus\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \LarasoftHU\LocalizedRoutesPlus\LocalizedRoutesPlus
 */
class LocalizedRoutesPlus extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \LarasoftHU\LocalizedRoutesPlus\LocalizedRoutesPlus::class;
    }
}
