<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Policy;
use App\Models\SinisterMultimedia;
use App\Models\SinisterComment;

use App\Enums\SinisterStatusEnum;

class Sinister extends Model
{
    /** @use HasFactory<\Database\Factories\SinisterFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'occur_date',
        'report_date',
        'close_date',
        'description',
        'location',
        'status',
        'adjuster_id',
        'supervisor_id',
        'policy_id'
    ];

    protected function casts(): array
    {
        return [
            'occur_date' => 'date',
            'report_date' => 'date',
            'close_date' => 'date',
            'description' => 'string',
            'location' => 'string',
            'status' => SinisterStatusEnum::class,
            'adjuster_id' => 'integer',
            'supervisor_id' => 'integer',
            'policy_id' => 'integer'
        ];
    }

    public function adjuster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjuster_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SinisterComment::class);
    }

    public function multimedia(): HasMany
    {
        return $this->hasMany(SinisterMultimedia::class);
    }
}
