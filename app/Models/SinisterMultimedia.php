<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Sinister;

use App\Enums\SinisterMultimediaTypeEnum;

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
            'type'        => SinisterMultimediaTypeEnum::class,
            // blob_file NO debe tener cast a string: PDO lo puede devolver como stream (resource)
            // y se debe leer con stream_get_contents() en MediaController
            'path_file'   => 'string',
            'description' => 'string',
            'mime'        => 'string',
            'size'        => 'integer',
            'thumbnail'   => 'string',
            'sinister_id' => 'integer'
        ];
    }

    public function sinister(): BelongsTo
    {
        return $this->belongsTo(Sinister::class);
    }
}
