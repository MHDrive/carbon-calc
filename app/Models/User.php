<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Plank\Metable\Metable;

class User extends Authenticatable implements HasMedia, HasAvatar
{
    use HasFactory, Notifiable, InteractsWithMedia, Metable;

    protected $with = ['institutes', 'media'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $media = $this->getFirstMediaUrl('avatars');

        // Jika media ditemukan, kembalikan URL-nya, jika tidak, kembalikan null
        return $media ?: null;
    }


    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 100, 100)
            ->nonQueued();
    }

    public function scopeMediaUserFirst($query, $collectionName)
    {
        return $query->whereHas('media', function ($q) use ($collectionName) {
            $q->where('collection_name', $collectionName);
        });
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'user_id', 'id');
    }

    public function institutes(): BelongsTo
    {
        return $this->belongsTo(Institute::class, 'institute_id', 'id');
    }
}
