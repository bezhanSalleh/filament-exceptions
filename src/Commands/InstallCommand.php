<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'exceptions:install')]
class InstallCommand extends Command
{
    public $signature = 'exceptions:install {--F|force}';

    public $description = 'Install `<b>Filament Exceptions</b>` plugin for filament.';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'filament-exceptions-migrations',
            '--force' => $this->option('force'),
        ]);

        $this->call('migrate', [
            '--force' => $this->option('force'),
        ]);

        $this->components->info('Filament Exceptions🐞... installed!');

        $this->askToStar();

        return self::SUCCESS;
    }

    protected function askToStar(): void
    {
        if ($this->option('no-interaction')) {
            return;
        }

        if (confirm(
            label: 'All done! Would you like to show some love by starring the `Filament Exceptions` repo on GitHub?',
            default: true,
        )) {
            if (PHP_OS_FAMILY === 'Darwin') {
                exec('open https://github.com/bezhanSalleh/filament-exceptions');
            }

            if (PHP_OS_FAMILY === 'Linux') {
                exec('xdg-open https://github.com/bezhanSalleh/filament-exceptions');
            }

            if (PHP_OS_FAMILY === 'Windows') {
                exec('start https://github.com/bezhanSalleh/filament-exceptions');
            }

            $this->components->info('Thank you!');
        }
    }
}
