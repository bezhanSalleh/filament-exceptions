<?php

namespace BezhanSalleh\FilamentExceptions\Commands;

use Illuminate\Console\Command;

class MakeExceptionsInstallCommand extends Command
{
    public $signature = 'exceptions:install';

    public $description = 'Installs filament exceptions plugin';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'filament-exceptions-migrations'
        ]);

        $this->components->info('Migration has been published');

        $this->call('migrate');

        $this->components->info('Filament Exceptions migration run.');

        $this->components->info('Add the following snippet to your exceptions');
        return self::SUCCESS;
    }
}
