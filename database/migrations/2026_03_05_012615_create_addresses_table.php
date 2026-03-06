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
            $table->id();
            $table->enum('type', [
                AddressTypeEnum::FISCAL->value,
                AddressTypeEnum::HOME->value,
                AddressTypeEnum::OFFICE->value
            ]);
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('hometown');
            $table->string('street');
            $table->string('external_number');
            $table->string('internal_number')->nullable();
            $table->string('zip_code');
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
        Schema::dropIfExists('addresses');
    }
};
