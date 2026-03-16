<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Sinister;
use App\Models\User;

class SinisterComment extends Model
{
    /** @use HasFactory<\Database\Factories\SinisterCommentFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'comment',
        'sinister_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'comment' => 'string',
            'sinister_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function sinister(): BelongsTo
    {
        return $this->belongsTo(Sinister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
