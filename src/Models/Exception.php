<?php

namespace BezhanSalleh\ExceptionPlugin\Models;

use BezhanSalleh\ExceptionPlugin\ExceptionPlugin;
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

    public function prunable(): \Illuminate\Database\Eloquent\Builder
    {
        return static::whereDate('created_at', '<=', ExceptionPlugin::get()->getModelPruneInterval());
    }
}
