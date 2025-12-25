<a href="https://github.com/bezhanSalleh/filament-exceptions" class="filament-hidden">
<img style="width: 100%; max-width: 100%;" alt="filament-exceptions-art" src="https://user-images.githubusercontent.com/10007504/188786069-e93f01a1-d910-4888-a29a-28eea4ee0458.jpg" >
</a>

<p align="center" class="flex items-center justify-center">
    <a href="https://filamentphp.com/docs/4.x/introduction/overview">
        <img alt="FILAMENT 4.x" src="https://img.shields.io/badge/FILAMENT-4.x-EBB304?style=for-the-badge">
    </a>
    <a href="https://packagist.org/packages/bezhansalleh/filament-exceptions">
        <img alt="Packagist" src="https://img.shields.io/packagist/v/bezhansalleh/filament-exceptions.svg?style=for-the-badge&logo=packagist">
    </a>
    <a href="https://github.com/bezhansalleh/filament-exceptions/actions?query=workflow%3Arun-tests+branch%3Amain" class="filament-hidden">
        <img alt="Tests Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-exceptions/run-tests.yml?style=for-the-badge&logo=github&label=tests">
    </a>
    <a href="https://github.com/bezhansalleh/filament-exceptions/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain" class="filament-hidden">
        <img alt="Code Style Passing" src="https://img.shields.io/github/actions/workflow/status/bezhansalleh/filament-exceptions/fix-php-code-style-issues.yml?style=for-the-badge&logo=github&label=code%20style">
    </a>

<a href="https://packagist.org/packages/bezhansalleh/filament-exceptions">
    <img alt="Downloads" src="https://img.shields.io/packagist/dt/bezhansalleh/filament-exceptions.svg?style=for-the-badge" >
    </a>
</p>

# Exception Viewer

A Simple & Beautiful Exception Viewer for FilamentPHP's Admin Panel

> **Version Compatibility:**
> - Filament 4.x → use version 4.x
> - Filament 3.x → use version 3.x
> - Filament 2.x → use version 1.x

## Installation

1. You can install the package via composer:

```bash
composer require bezhansalleh/filament-exceptions
```

2. Publish and run the migration via:
```bash
php artisan exceptions:install
```

3. Register the plugin for the Filament Panel

```php
public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            \BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin::make()
        ]);
}
```

That's it! The package automatically registers itself with Laravel's exception handler and starts recording exceptions.


> [!IMPORTANT]
> The plugin follows Filament's theming rules. So, to use the plugin create a custom theme if you haven't already, and add the following line to your `theme.css` file:

```php
@source '../../../../vendor/bezhansalleh/filament-exceptions/resources/views/**/*.blade.php';
```
Now build your theme using: 
```bash
npm run build
```


### Recording Control

By default, exceptions are recorded automatically. You can control this behavior in your `AppServiceProvider`'s `boot()` method:

#### Stop/Start Recording
```php
use BezhanSalleh\FilamentExceptions\FilamentExceptions;

public function boot(): void
{
    // Stop recording exceptions
    FilamentExceptions::stopRecording();

    // Resume recording
    FilamentExceptions::startRecording();

    // Check if recording is active
    FilamentExceptions::isRecording();
}
```

#### Conditional Recording
Use `recordUsing()` to define custom logic for when exceptions should be recorded:

```php
use BezhanSalleh\FilamentExceptions\FilamentExceptions;

public function boot(): void
{
    // Only record in production
    FilamentExceptions::recordUsing(fn () => app()->isProduction());

    // Skip specific exception types
    FilamentExceptions::recordUsing(function (Throwable $e) {
        return ! $e instanceof \Illuminate\Validation\ValidationException
            && ! $e instanceof \Illuminate\Auth\AuthenticationException;
    });

    // Combine multiple conditions
    FilamentExceptions::recordUsing(function (Throwable $e) {
        if (! app()->isProduction()) {
            return false;
        }

        // Skip 4xx HTTP exceptions
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException
            && $e->getStatusCode() < 500) {
            return false;
        }

        return true;
    });
}
```

### Configuration Options

When registering the FilamentExceptions plugin, you can chain various methods to customize its behavior. Here are all available configuration options:

#### Navigation
```php
FilamentExceptionsPlugin::make()
    ->navigationBadge(bool | Closure $condition = true)
    ->navigationBadgeColor(string | array | Closure $color)
    ->navigationGroup(string | Closure | null $group)
    ->navigationParentItem(string | Closure | null $item)
    ->navigationIcon(string | Closure | null $icon)
    ->activeNavigationIcon(string | Closure | null $icon)
    ->navigationLabel(string | Closure | null $label)
    ->navigationSort(int | Closure | null $sort)
    ->registerNavigation(bool | Closure $shouldRegisterNavigation)
    ->subNavigationPosition(SubNavigationPosition | Closure $position)
```

#### Labels and Search
```php
FilamentExceptionsPlugin::make()
    ->modelLabel(string | Closure | null $label)
    ->pluralModelLabel(string | Closure | null $label)
    ->titleCaseModelLabel(bool | Closure $condition = true)
    ->globallySearchable(bool | Closure $condition = true)
```

#### Mass Pruning Settings
```php
FilamentExceptionsPlugin::make()
    ->modelPruneInterval(Carbon $interval)
```
> **Note** This requires laravel scheduler to be setup and configured in order to work. You can see how to do that here  [Running The Scheduler](https://laravel.com/docs/10.x/scheduling#running-the-scheduler)

#### Tenancy Configuration
```php
FilamentExceptionsPlugin::make()
    ->scopeToTenant(bool | Closure $condition = true)
    ->tenantOwnershipRelationshipName(string | Closure | null $ownershipRelationshipName)
    ->tenantRelationshipName(string | Closure | null $relationshipName)
```

#### General Configuration
```php
FilamentExceptionsPlugin::make()
    ->cluster(string | Closure | null $cluster)
    ->slug(string | Closure | null $slug)
```

Example usage:
```php
return $panel
    ->plugins([
        FilamentExceptionsPlugin::make()
            ->navigationLabel('Error Logs')
            ->navigationIcon('heroicon-o-bug-ant')
            ->navigationBadge()
            ->navigationGroup('System')
            ->modelPruneInterval(now()->subDays(7))
    ]);
```
 
### Custom Exception Model
1. Extend the base model as follow:
```php
<?php

namespace App\Models;

use BezhanSalleh\FilamentExceptions\Models\Exception as BaseException;

class MyCustomException extends BaseException
{
    ...
}
```
2. Then, in a service provider's `boot()` method for instance `AppServiceProvider`:
```php
use App\Models\MyCustomException;
use BezhanSalleh\FilamentExceptions\FilamentExceptions;
...
   public function boot()
   {
       FilamentExceptions::model(MyCustomException::class);
   }
...
```

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
