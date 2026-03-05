<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\FiscalTypeEnum;
use App\Models\Fiscal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fiscals', function (Blueprint $table) {
            $table->id();
            $table->string('rfc')->unique();
            $table->enum('fiscal_type', [
                FiscalTypeEnum::LEGAL_PERSON->value,
                FiscalTypeEnum::NATURAL_PERSON->value
            ])->default(FiscalTypeEnum::NATURAL_PERSON->value);
            $table->string('company_name')->nullable(); //razon social
            $table->string('tax_regime', 3);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fiscals');
    }
};
