<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\User;
use App\Models\VehicleModel;

class InsuredVehicle extends Model
{
    /** @use HasFactory<\Database\Factories\VehicleFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'vin',
        'plate',
        'user_id',
        'vehicle_model_id'
    ];

    protected function casts(): array
    {
        return [
            'vin' => 'string',
            'plate' => 'string',
            'user_id' => 'integer',
            'vehicle_model_id' => 'integer'
        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->vehicleModel->brand} {$this->vehicleModel->sub_brand} {$this->vehicleModel->version} {$this->vehicleModel->year} - {$this->color}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicleModel(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function policy()
    {
        return $this->hasOne(\App\Models\Policy::class, 'vehicle_id');
    }
}
