<?php

namespace LarasoftHU\LocalizedRoutesPlus\Commands;

use Illuminate\Console\Command;

class LocalizedRoutesPlusCommand extends Command
{
    public $signature = 'localized-routes-plus';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
