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
            $table->id();
            $table->uuid('folio')->unique();
            $table->enum('status', [
                PolicyStatusEnum::PENDING->value,
                PolicyStatusEnum::ACTIVE->value,
                PolicyStatusEnum::CANCELLED->value,
                PolicyStatusEnum::EXPIRED->value,
            ])->default(PolicyStatusEnum::PENDING->value);
            $table->date('begin_validity');
            $table->date('end_validity');
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->foreignId('insured_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained('plans')->cascadeOnDelete();
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
