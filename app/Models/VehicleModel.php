<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\InsuredVehicle;

class VehicleModel extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleModelFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'year',
        'brand',
        'sub_brand',
        'version',
        'color'
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'brand' => 'string',
            'sub_brand' => 'string',
            'version' => 'string',
            'color' => 'string',
        ];
    }

    public function insuredVehicles(): HasMany
    {
        return $this->hasMany(InsuredVehicle::class);
    }
}
