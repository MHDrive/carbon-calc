<?php

namespace App\Models;

use App\Models\Calculation as Model;
use Illuminate\Support\Facades\Auth;

class Recapitulation extends Model
{
    protected $table = 'calculations';
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope('user_id', function ($builder) {
    //         $builder->where('user_id', Auth::id());
    //     });
    // }
}
