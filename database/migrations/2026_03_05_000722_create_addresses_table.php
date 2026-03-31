<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\AddressTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de direcciones');
            $table->string('type')->default(AddressTypeEnum::HOME->value)->comment('Tipo de direccion que se registra');
            $table->string('country')->comment('Pais asociado a la direccion');
            $table->string('state')->comment('Estado asociado a la direccion');
            $table->string('city')->comment('Ciudad asociada a la direccion');
            $table->string('neighborhood')->comment('Colonia asociada a la direccion');
            $table->string('street')->comment('Calle asociada a la direccion');
            $table->string('external_number', 10)->comment('Numero exterior de la direccion');
            $table->string('internal_number', 10)->nullable()->comment('Numero interior de la direccion');
            $table->string('zip_code', 10)->comment('Codigo postal asociado a la direccion');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
