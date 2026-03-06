<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Models\Policy;

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
            'description' => 'text',
            'location' => 'string',
            'status' => 'string',
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
}
