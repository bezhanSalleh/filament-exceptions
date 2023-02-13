<a href="https://github.com/bezhanSalleh/filament-exceptions">
<img style="width: 100%; max-width: 100%;" alt="filament-exceptions-art" src="https://user-images.githubusercontent.com/10007504/188786069-e93f01a1-d910-4888-a29a-28eea4ee0458.jpg" >
</a>

<p align="center">
    <a href="https://filamentadmin.com/docs/2.x/admin/installation">
        <img alt="FILAMENT 8.x" src="https://img.shields.io/badge/FILAMENT-2.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://packagist.org/packages/bezhansalleh/filament-exceptions">
        <img alt="Packagist" src="https://img.shields.io/packagist/v/bezhansalleh/filament-exceptions.svg?style=for-the-badge&logo=packagist">
    </a>
    <a href="https://github.com/bezhansalleh/filament-exceptions/actions?query=workflow%3Arun-tests+branch%3Amain">
        <img alt="Tests Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-exceptions/run-tests.yml?style=for-the-badge&logo=github&label=tests">
    </a>
    <a href="https://github.com/bezhansalleh/filament-exceptions/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain">
        <img alt="Code Style Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-exceptions/fix-php-code-style-issues.yml?style=for-the-badge&logo=github&label=code%20style">
    </a>

<a href="https://packagist.org/packages/bezhansalleh/filament-exceptions">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/bezhansalleh/filament-exceptions.svg?style=for-the-badge" >
    </a>
</p>

# Filament Exception Viewer

A Simple & Beautiful Exception Viewer for FilamentPHP's Admin Panel



## Installation

You can install the package via composer:

```bash
composer require bezhansalleh/filament-exceptions
```

Publish and run the migration via:
```bash
php artisan exceptions:install
```

Activate the plugin by editing your App's Exception Handler as follow:

```php
<?php

namespace App\Exceptions;

use BezhanSalleh\FilamentExceptions\FilamentExceptions;

class Handler extends ExceptionHandler
{
    ...

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($this->shouldReport($e)) {
                FilamentExceptions::report($e);
            }
        });

        ...
    }
```

## Configuration
The configuration file filament-exceptions.php is automatically published into your config directory.
You can change icons and navigations settings as well as the active pill and slug there.

## Translations
Publish the translations with
```bash
php artisan vendor:publish --tag=filament-exceptions-translations
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bezhan Salleh](https://github.com/bezhanSalleh)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
