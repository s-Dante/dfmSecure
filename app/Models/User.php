<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Role;
use App\Models\Fiscal;
use App\Models\Address;
use App\Models\InsuredVehicle;

use App\Enums\AddressTypeEnum;

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
        'email',
        'password',
        'phone',
        'gender_id',
        'role_id'
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
            'email' => 'string',
            'password' => 'hashed',
            'phone' => 'string',
            'gender_id' => 'integer',
            'role_id' => 'integer',
            'email_verified_at' => 'datetime',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->father_lastname} {$this->mother_lastname}";
    }

    public function mainAddress()
    {
        return $this->hasOne(Address::class)->where('type', AddressTypeEnum::HOME);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function fiscalData(): HasOne
    {
        return $this->hasOne(Fiscal::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(InsuredVehicle::class);
    }
}
