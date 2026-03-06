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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->uuid('folio')->unique();
            $table->string('status');
            $table->date('begin_validity');
            $table->date('end_validity');
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('insured_id')->constrained('users');
            $table->foreignId('plan_id')->constrained('plans');
            $table->timestamps();
            $table->softDeletes();
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
