<?php

namespace App\Models;

use App\Models\Traits\HasDatetimeFormatterTz;
use Fomvasss\MediaLibraryExtension\HasMedia\HasMedia;
use Fomvasss\MediaLibraryExtension\HasMedia\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasMedia
{
    use HasFactory,
        Notifiable,
        HasUuids,
        HasApiTokens,
        HasDatetimeFormatterTz,
        InteractsWithMedia;

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'extra' => 'array',
            'email_verified_at' => 'datetime',
            'registered_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'float',
        ];
    }

    protected array $mediaSingleCollections = ['avatar'];

    /**
     * Socialite auth мережі.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function socialites()
    {
        return $this->hasMany(Socialite::class);
    }

    public function aijobs()
    {
        return $this->hasMany(AIJob::class);
    }

    /**
     * @return Attribute
     */
    protected function fullname(): Attribute
    {
        return Attribute::make(
            get: fn () => trim($this->lastname . ' ' . $this->name),
        );
    }
}
