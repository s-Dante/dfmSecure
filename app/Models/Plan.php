<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Policy;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'status',
        'info'
    ];

    protected function casts(): array {
        return [
            'name' => 'string',
            'status' => 'string',
            'info' => 'json'
        ];
    }

    public function policy(): HasMany {
        return $this->hasMany(Policy::class);
    }
}
