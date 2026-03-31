<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('insured_vehicles', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de vehiculos asegurados');
            $table->string('vin', 17)->unique()->comment('Numero de identificacion vehicular');
            $table->string('plate', 10)->unique()->comment('Placa del vehiculo');
            $table->foreignId('vehicle_model_id')->constrained('vehicle_models')->cascadeOnDelete()->comment('Identificador del modelo del vehiculo');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->comment('Identificador del usuario');
            $table->timestamps();
            $table->softDeletes();
            $table->index('vin');
            $table->index('plate');
            $table->index('vehicle_model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insured_vehicles');
    }
};
