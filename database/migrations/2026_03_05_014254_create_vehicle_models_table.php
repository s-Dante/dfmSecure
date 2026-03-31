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
        Schema::create('vehicle_models', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de modelos de vehiculos');
            $table->year('year')->comment('Año del modelo');
            $table->string('brand')->comment('Marca del modelo');
            $table->string('sub_brand')->comment('Submarca del modelo');
            $table->string('version')->comment('Version del modelo');
            $table->string('color')->comment('Color del modelo');
            $table->unique(['year', 'brand', 'sub_brand', 'version', 'color']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_models');
    }
};
