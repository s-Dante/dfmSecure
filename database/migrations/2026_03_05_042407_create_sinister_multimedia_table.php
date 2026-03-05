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
        Schema::create('sinister_multimedia', function (Blueprint $table) {
            $table->id();
            $table->string('type'); //enum
            $table->longText('blob_file')->nullable();
            $table->longText('path_file')->nullable();
            $table->text('description')->nullable();
            $table->string('mime');
            $table->string('size');
            $table->string('thumbnail');
            $table->foreignId('sinister_id')->constrained('sinisters');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinister_multimedia');
    }
};
