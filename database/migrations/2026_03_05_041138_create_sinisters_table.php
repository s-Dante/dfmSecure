<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->date('close_date');
            $table->text('description');
            $table->string('ublication'); //Checar bien
            $table->string('status');
            $table->foreignId('adjuster_id')->constrained('users');
            $table->foreignId('supervisor_id')->constrained('users');
            $table->foreignId('plicy_id')->constrained('policies');
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
