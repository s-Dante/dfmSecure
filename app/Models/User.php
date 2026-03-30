<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'father_lastname',
        'mother_lastname',
        'username',
        'profile_picture_url',
        'profile_picture_blob',
        'email',
        'password',
        'phone',
        'birth_date',
        'gender',
        'role_id',
        'address_id',
    ];

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
            'name' => 'string',
            'father_lastname' => 'string',
            'mother_lastname' => 'string',
            'username' => 'string',
            'profile_picture_url' => 'string',
            'email' => 'string',
            'password' => 'hashed',
            'phone' => 'string',
            'birth_date' => 'date',
            'gender_id' => 'integer',
            'role_id' => 'integer',
            'address_id' => 'integer',
            'email_verified_at' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->father_lastname} {$this->mother_lastname}";
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function fiscalData(): HasOne
    {
        return $this->hasOne(Fiscal::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(InsuredVehicle::class);
    }

    public function policies(): HasMany
    {
        return $this->hasMany(Policy::class, 'insured_id');
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role && $this->role->name === $role;
    }

    public function isInsured(): bool
    {
        return $this->hasRole(\App\Enums\RoleEnum::INSURED->value);
    }

    public function isAdjuster(): bool
    {
        return $this->hasRole(\App\Enums\RoleEnum::ADJUSTER->value);
    }

    public function isSupervisor(): bool
    {
        return $this->hasRole(\App\Enums\RoleEnum::SUPERVISOR->value);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(\App\Enums\RoleEnum::ADMIN->value);
    }

}
