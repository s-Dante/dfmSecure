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
            $table->string('rfc', 13)->unique();
            $table->string('fiscal_type')->default(FiscalTypeEnum::NATURAL_PERSON->value);
            $table->string('company_name')->nullable(); //razon social
            $table->string('tax_regime', 5);
            $table->foreignId('user_id')->constrained('users')->unique();
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
