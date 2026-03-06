<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Sinister;

class SinisterMultimedia extends Model
{
    /** @use HasFactory<\Database\Factories\SinisterMultimediaFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'blob_file',
        'path_file',
        'description',
        'mime',
        'size',
        'thumbnail',
        'sinister_id'
    ];

    public function casts(): array
    {
        return [
            'type' => 'string',
            'blob_file' => 'string',
            'path_file' => 'string',
            'description' => 'text',
            'mime' => 'string',
            'size' => 'string',
            'thumbnail' => 'string',
            'sinister_id' => 'integer'
        ];
    }

    public function sinister(): BelongsTo
    {
        return $this->belongsTo(Sinister::class);
    }
}
