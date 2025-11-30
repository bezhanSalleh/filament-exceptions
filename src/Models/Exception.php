<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Models;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    use MassPrunable;

    protected $table = 'filament_exceptions_table';

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    // protected function casts(): array
    // {
    //     return [
    //         'headers' => 'array',
    //         'cookies' => 'array',
    //         'body' => 'array',
    //     ];
    // }

    public function prunable(): Builder
    {
        return static::whereDate('created_at', '<=', FilamentExceptionsPlugin::get()->getModelPruneInterval());
    }

    public function getQueryAttribute($value): array
    {
        return collect(json_decode((string) $value, true))
            ->filter(fn ($q): bool => filled($q))
            ->all() ?? [];
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: fn ($value): array => $this->transformAttribute($value),
        );
    }

    protected function headers(): Attribute
    {
        return Attribute::make(
            get: fn ($value): array => $this->transformAttribute($value),
        );
    }

    protected function cookies(): Attribute
    {
        return Attribute::make(
            get: fn ($value): array => $this->transformAttribute($value),
        );
    }

    protected function transformAttribute($value): array
    {
        return collect(json_decode((string) $value, true))
            ->sortKeys()
            ->transform(fn ($val): mixed => is_array($val) ? implode(' ', collect($val)->flatten()->toArray()) : $val)
            ->filter()
            ->all();
    }
}
