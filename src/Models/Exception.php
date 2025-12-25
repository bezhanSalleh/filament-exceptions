<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentExceptions\Models;

use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $type
 * @property string $code
 * @property string $message
 * @property string $file
 * @property int $line
 * @property array<int, array<string, mixed>> $trace
 * @property string $method
 * @property string $path
 * @property string|null $ip
 * @property array<int, array<string, mixed>> $query
 * @property array<string, array<int, string>|string> $headers
 * @property array<string, mixed>|null $body
 * @property array<string, mixed>|null $cookies
 * @property array<string, string>|null $route_context
 * @property array<string, mixed>|null $route_parameters
 * @property string|null $markdown
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
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
            'markdown' => 'string',
        ];
    }
}
