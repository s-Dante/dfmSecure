<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\PlanStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de planes');
            $table->string('name')->unique()->comment('Nombre del plan');
            $table->string('status')->default(PlanStatusEnum::ACTIVE->value)->comment('Estado del plan');
            $table->json('info')->comment('Informacion del plan'); //que use utf8mb4_unicode_ci
            $table->decimal('price', 10, 2)->comment('Precio del plan');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
