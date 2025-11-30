<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Models;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Exception extends Model
{
    use MassPrunable;

    protected $table = 'filament_exceptions_table';

    protected $guarded = [];

    public function prunable(): Builder
    {
        return static::whereDate('created_at', '<=', FilamentExceptionsPlugin::get()->getModelPruneInterval());
    }

    protected function casts(): array
    {
        return [
            'line' => 'integer',
            'trace' => 'array',
            'headers' => 'array',
            'cookies' => 'array',
            'body' => 'array',
            'query' => 'array',
            'route_context' => 'array',
            'route_parameters' => 'array',
        ];
    }
}
