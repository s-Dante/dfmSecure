<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\User;
use App\Models\Policy;

class Sinister extends Model
{
    /** @use HasFactory<\Database\Factories\SinisterFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillables = [
        'occur_date',
        'report_date',
        'close_date',
        'description',
        'ubication',
        'status',
        'adjuster_id',
        'supervisor_id',
        'policy_id'
    ];

    protected function casts(): array {
        return [
            'occur_date' => 'date',
            'reprot_date' => 'date',
            'close_date' => 'date',
            'description' => 'text',
            'ubication' => 'string',
            'status' => 'string',
            'adjuster_id' => 'integer',
            'supervisor_id' => 'integer',
            'policy_id' => 'integer'
        ];
    }

    public function adjuster(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }

    public function policy(): BelongsTo {
        return $this->belongsTo(Policy::class);
    }
}
