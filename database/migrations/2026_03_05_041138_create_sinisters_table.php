<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\SinisterStatusEnum;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinisters', function (Blueprint $table) {
            $table->id();
            $table->date('occur_date');
            $table->date('report_date');
            $table->date('close_date')->nullable();
            $table->text('description');
            $table->string('location'); //Checar bien
            $table->enum('status', [
                SinisterStatusEnum::REPORTED->value,
                SinisterStatusEnum::REJECTED->value,
                SinisterStatusEnum::IN_REVIEW->value,
                SinisterStatusEnum::APPROVED->value,
                SinisterStatusEnum::APPROVED_WITH_DEDUCTIBLE->value,
                SinisterStatusEnum::APPROVED_WITHOUT_DEDUCTIBLE->value,
                SinisterStatusEnum::APPLIES_PAYMENT_FOR_REPAIRS->value,
                SinisterStatusEnum::TOTAL_LOSS->value,
                SinisterStatusEnum::CLOSED->value
            ])->default(SinisterStatusEnum::REPORTED->value);
            $table->foreignId('adjuster_id')->constrained('users');
            $table->foreignId('supervisor_id')->constrained('users')->nullable();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinisters');
    }
};
