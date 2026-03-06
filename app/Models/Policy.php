<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\Plan;

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
            'folio' => 'uuid',
            'status' => 'string',
            'begin_validity' => 'date',
            'end_validity' => 'date',
            'vehicle_id' => 'integer',
            'insured_id' => 'integer',
            'plan_id' => 'integer'
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'insured_id');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
