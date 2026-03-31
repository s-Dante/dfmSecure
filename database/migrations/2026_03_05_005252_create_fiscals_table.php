<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\FiscalTypeEnum;
use App\Models\Fiscal;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fiscals', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de datos fiscales');
            $table->string('rfc', 13)->unique()->comment('Registro Federal de Contribuyentes');
            $table->string('fiscal_type')->default(FiscalTypeEnum::NATURAL_PERSON->value)->comment('Tipo de entidad ante la ley');
            $table->string('company_name')->nullable()->comment('Razon social');
            $table->string('tax_regime', 5)->comment('Regimen fiscal');
            $table->foreignId('user_id')->constrained('users')->unique()->comment('Identificador del usuario');
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
