<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\UserRoleEnum;
use Filament\Panel;
use Plank\Metable\Metable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser, HasMedia, HasAvatar, HasName
{
    use HasFactory, Notifiable, InteractsWithMedia, Metable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'roles',
        'institute_id',
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

    public function canAccessPanel(Panel $panel): bool
    {
        return  $this->isAdmin() || $this->isGuest();
    }

    public function isAdmin()
    {
        return $this->roles === UserRoleEnum::ADMIN->value;
    }
    public function isGuest()
    {
        return $this->roles === UserRoleEnum::GUEST->value;
    }

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
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
