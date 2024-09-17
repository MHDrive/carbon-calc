<?php

namespace App\Models;

use App\Models\User;
use App\Models\Calculation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recapitulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calculation_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function calculation(): BelongsTo
    {
        return $this->belongsTo(Calculation::class);
    }
}
