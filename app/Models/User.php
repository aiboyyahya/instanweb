<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'phone',
        'address',
        'profile_photo',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'email_verification_code',
        'phone_verification_code',
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
            'phone_verified_at' => 'datetime',
            'email_verification_expires_at' => 'datetime',
            'email_verification_sent_at' => 'datetime',
            'phone_verification_expires_at' => 'datetime',
            'phone_verification_sent_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Toko yang dimiliki pengguna (jika ia pemilik toko).
     */
    public function store()
    {
        return $this->hasOne(Store::class);
    }

    public function isStoreOwner(): bool
    {
        return $this->role === 'admin';
    }

    public function hasVerifiedPhone(): bool
    {
        return ! is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified(): bool
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
            'phone_verification_code' => null,
            'phone_verification_expires_at' => null,
            'phone_verification_sent_at' => null,
        ])->save();
    }

    /**
     * Hanya pemilik toko/admin yang boleh membuka panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }
}
