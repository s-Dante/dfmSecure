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
            $table->string('location');
            $table->string('status')->default(SinisterStatusEnum::REPORTED->value);
            $table->foreignId('adjuster_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->index('adjuster_id');
            $table->index('supervisor_id');
            $table->index('policy_id');
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
