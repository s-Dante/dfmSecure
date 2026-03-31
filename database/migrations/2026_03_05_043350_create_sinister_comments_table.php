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
        Schema::create('sinister_comments', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de comentarios de siniestros');
            $table->longText('comment')->comment('Comentario del siniestro');
            $table->foreignId('sinister_id')->constrained('sinisters')->comment('Identificador del siniestro');
            $table->foreignId('user_id')->constrained('users')->comment('Identificador del usuario');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinister_comments');
    }
};
