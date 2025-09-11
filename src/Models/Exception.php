<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Models;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Exception extends model
{
    use MassPrunable;

    protected $table = 'filament_exceptions_table';

    /**
     * @var array
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

    public function getQueryAttribute($value): array
    {
        return collect(json_decode((string) $value, true))
            ->filter(fn ($q): bool => filled($q))
            ->all() ?? [];
    }

    protected function transformAttribute($value): array
    {
        return collect(json_decode((string) $value, true))
            ->sortKeys()
            ->transform(fn ($val) => is_array($val) ? implode(' ', collect($val)->flatten()->toArray()) : $val)
            ->filter()
            ->all();
        // return collect(json_decode($value, true))
        // ->transform(function ($val) {
        //     if (is_array($val)) {
        //         // Check if it is a simple array (all items are strings or numbers)
        //         $isSimple = collect($val)->every(fn($item) => is_string($item) || is_numeric($item));

        //         return $isSimple
        //             ? str_replace(',', ' | ', implode(',', $val))  // simple array -> join and replace
        //             : json_encode($val);                           // complex array -> encode as string
        //     }

        //     return $val; // keep strings/numbers as is
        // })
        // ->all();
    }
}
