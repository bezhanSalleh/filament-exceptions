<?php

namespace BezhanSalleh\FilamentExceptions\Models;

use Illuminate\Database\Eloquent\Model;

class Exception extends model
{
    protected $table = 'filament_exceptions_table';

    /**
     * @var array
     */
    protected $guarded = [];
}
