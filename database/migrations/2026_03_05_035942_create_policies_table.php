<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\PolicyStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de polizas');
            $table->uuid('folio')->unique()->comment('Folio de la poliza');
            $table->string('policy_number')->nullable()->unique()->comment('Numero de poliza generado por trigger (POL-000001)');
            $table->string('status')->default(PolicyStatusEnum::PENDING->value)->comment('Estado de la poliza');
            $table->date('begin_validity')->comment('Fecha de inicio de vigencia');
            $table->date('end_validity')->comment('Fecha de fin de vigencia');
            $table->foreignId('vehicle_id')->constrained('insured_vehicles')->cascadeOnDelete()->comment('Identificador del vehiculo');
            $table->foreignId('insured_id')->constrained('users')->cascadeOnDelete()->comment('Identificador del asegurado');
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete()->comment('Identificador del plan');
            $table->timestamps();
            $table->softDeletes();
            $table->index('vehicle_id');
            $table->index('insured_id');
            $table->index('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
