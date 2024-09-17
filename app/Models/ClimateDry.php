<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Calculation as Model;

class ClimateDry extends Model
{
    use HasFactory;

    protected $table = 'calculations';
}
