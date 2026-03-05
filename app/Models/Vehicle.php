<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;

class Vehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'year',
        'brand',
        'sub_brand',
        'version',
        'color',
        'vin',
        'plate',
        'user_id'
    ];

    protected function casts(): array {
        return [
            'year' => 'string',
            'brand' => 'string',
            'sub_brand' => 'string',
            'version' => 'string',
            'color' => 'string',
            'vin' => 'string',
            'plate' => 'string',
            'user_id' => 'integer'
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
