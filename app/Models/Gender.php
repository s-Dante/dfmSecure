<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Gender extends Model
{
    /** @use HasFactory<\Database\Factories\GenderFactory> */
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

    public function user(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
