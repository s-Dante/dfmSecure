<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name'
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string'
        ];
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }
}
