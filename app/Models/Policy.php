<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\InsuredVehicle;
use App\Models\User;
use App\Models\Plan;

use App\Enums\PolicyStatusEnum;

class Policy extends Model
{
    /** @use HasFactory<\Database\Factories\PolicyFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'folio',
        'status',
        'begin_validity',
        'end_validity',
        'vehicle_id',
        'insured_id',
        'plan_id'
    ];

    protected function casts(): array
    {
        return [
            'folio' => 'string',
            'status' => PolicyStatusEnum::class,
            'begin_validity' => 'date',
            'end_validity' => 'date',
            'vehicle_id' => 'integer',
            'insured_id' => 'integer',
            'plan_id' => 'integer'
        ];
    }

    public function isActive(): bool
    {
        return now()->between($this->begin_validity, $this->end_validity);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->end_validity);
    }

    public function scopeActive($query)
    {
        return $query->where('status', PolicyStatusEnum::ACTIVE);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(InsuredVehicle::class);
    }

    public function insured(): BelongsTo
    {
        return $this->belongsTo(User::class, 'insured_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
