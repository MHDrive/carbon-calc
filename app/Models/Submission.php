<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use App\Enums\StatusSubmissionEnum;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Submission extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['user_id', 'notes', 'status', 'is_active', 'granted_at', 'expires_at'];

    // protected $with = ['media'];

    protected $casts = [
        'status' => StatusSubmissionEnum::class,
        'is_active' => StatusSubmissionEnum::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function institute(): HasOneThrough
    {
        return $this->hasOneThrough(Institute::class, User::class, 'id', 'id', 'user_id', 'institute_id');
    }

    public function scopeRouteGuest($query)
    {
        return $query->where(function ($query) {
            $query->where('status', StatusSubmissionEnum::PENDING)
                ->orWhere('status', StatusSubmissionEnum::APPROVED);
        })->where('is_active', true);
    }
}
