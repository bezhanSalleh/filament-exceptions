<?php

namespace BezhanSalleh\FilamentExceptions\Commands;

use Illuminate\Console\Command;

class MakeExceptionsInstallCommand extends Command
{
    public $signature = 'exceptions:install';

    public $description = 'Installs filament exceptions plugin';

    public function handle(): int
    {
        $this->components->info('Installing Filament Exceptions ... 🐞');

        $this->call('vendor:publish', [
            '--tag' => 'filament-exceptions-config',
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'filament-exceptions-migrations',
        ]);

        $this->call('migrate');

        $this->comment('Now activate the plugin for your app by adding the');
        $this->comment('following snippet into your App\'s Exception Handlers\'s reportable method.');
        $this->newLine();
        $this->info('        if ($this->shouldReport($e)) {
            FilamentExceptions::report($e);
        }');

        return self::SUCCESS;
    }
}
