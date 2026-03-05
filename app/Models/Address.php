<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Enums\FiscalTypeEnum;

use App\Models\User;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'type',
        'cpuntry',
        'state',
        'city',
        'hometown',
        'street',
        'external_number',
        'internal_number',
        'zip_code'
    ];

    protected function casts():array {
        return [
            'type' => FiscalTypeEnum::class,
            'country' => 'string',
            'state' => 'string',
            'city' => 'string',
            'hometown' => 'string',
            'country' => 'string',
            'street' => 'string',
            'external_number' => 'string',
            'internal_number' => 'string',
            'zip_code' => 'string',
            'user_id' => 'integer'
        ];
    }

    public function user(): BelongsToMany {
        return $this->belongsToMany(User::class);
    }
}
