<?php

namespace App\Models;

use App\Enums\AddressTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\FiscalTypeEnum;

use App\Models\User;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'country',
        'state',
        'city',
        'neighborhood',
        'street',
        'external_number',
        'internal_number',
        'zip_code',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'type' => AddressTypeEnum::class,
            'country' => 'string',
            'state' => 'string',
            'city' => 'string',
            'hometown' => 'string',
            'street' => 'string',
            'external_number' => 'string',
            'internal_number' => 'string',
            'zip_code' => 'string',
            'user_id' => 'integer'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
