<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements
    FilamentUser,
    HasAvatar,
    HasAppAuthentication,
    MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'photo_url',
        'password',
        'is_active',
        'blocked_at',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'app_authentication_secret',
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
            'blocked_at' => 'datetime',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
            'app_authentication_secret' => 'encrypted',

        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (! $this->photo_url) {
            return null;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($this->photo_url)) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        return $disk->url($this->photo_url);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->can('panel.admin.access');
        }
        if ($panel->getId() === 'planificacion') {
            return $this->can('panel.planificacion.access');
        }
        return false;
    }

    public function getAppAuthenticationSecret(): ?string
    {
        // This method should return the user's saved app authentication secret.

        return $this->app_authentication_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        // This method should save the user's app authentication secret.

        $this->app_authentication_secret = $secret;
        $this->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        // In a user's authentication app, each account can be represented by a "holder name".
        // If the user has multiple accounts in your app, it might be a good idea to use
        // their email address as then they are still uniquely identifiable.

        return $this->email;
    }

    /**
     * -----------------------------
     * Relaciones
     * -----------------------------
     */

    // Quién creó este usuario
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Perfil instructor (si existe)
    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }
    
}
