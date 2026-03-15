<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\PlanStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('status', [
                PlanStatusEnum::ACTIVE->value,
                PlanStatusEnum::INACTIVE->value,
                PlanStatusEnum::DELETED->value,
            ])->default(PlanStatusEnum::ACTIVE->value);
            $table->json('info');
            $table->decimal('price',10,2);
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
