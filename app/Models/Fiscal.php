<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Enums\FiscalTypeEnum;
use App\Enums\TaxRegimeEnum;

use App\Models\User;

class Fiscal extends Model
{
    /** @use HasFactory<\Database\Factories\FiscalFactory> */
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'rfc',
        'fiscal_type',
        'company_name',
        'tax_regime',
        'user_id',
    ];

    protected function casts(): array 
    {
        return [
            'rfc' => 'string',
            'fiscal_type' => FiscalTypeEnum::class,
            'company_name' => 'string',
            'tax_regime' => TaxRegimeEnum::class,
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
