<?php

namespace BezhanSalleh\FilamentExceptions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;

class Exception extends model
{
    use MassPrunable;

    protected $table = 'filament_exceptions_table';

    /**
     * @var array
     */
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'cookies' => 'array',
            'body' => 'array',
        ];
    }

    public function prunable(): Builder
    {
        return static::whereDate('created_at', '<=', FilamentExceptionsPlugin::get()->getModelPruneInterval());
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->transformAttribute($value),
        );
    }

    protected function headers(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->transformAttribute($value),
        );
    }

    protected function cookies(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->transformAttribute($value),
        );
    }

    public function getQueryAttribute($value): array
    {
        return collect(json_decode($value,true))
            ->filter(fn ($q) => filled($q))
            ->all() ?? [];
    }

    protected function transformAttribute($value): array
    {
        return collect(json_decode($value, true))
            ->sortKeys()
            ->transform(function ($val) {
                return is_array($val) ? implode(' ', collect($val)->flatten()->toArray()) : $val;
            })
            ->filter()
            ->all();
    }
}
