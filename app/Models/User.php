<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_DOSEN = 'dosen';
    const ROLE_MAHASISWA = 'mahasiswa';
    const ROLE_GUEST = 'guest';

    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'role',
        'is_verified',
        'department_id',
    ];

    public function isSuperAdmin(): bool { return $this->role === self::ROLE_SUPERADMIN; }
    public function isAdmin(): bool { return in_array($this->role, [self::ROLE_SUPERADMIN, self::ROLE_ADMIN]); }
    public function isDosen(): bool { return $this->role === self::ROLE_DOSEN; }
    public function isGuest(): bool { return $this->role === self::ROLE_GUEST; }
    public function isMahasiswa(): bool { return $this->role === self::ROLE_MAHASISWA; }

    public function getIdentityLabel(): string
    {
        return match($this->role) {
            self::ROLE_DOSEN => 'NIDN',
            self::ROLE_GUEST => 'No. Identitas / KTP',
            default => 'NIM',
        };
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the theses for the user.
     */
    public function theses(): HasMany
    {
        return $this->hasMany(Thesis::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
}
