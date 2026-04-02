<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\SinisterStatusEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinisters', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de siniestros');
            $table->uuid('folio')->unique()->comment('Folio del siniestro');
            $table->string('sinister_number')->nullable()->unique()->comment('Numero de siniestro generado por trigger (SIN-000001)');
            $table->date('occur_date')->comment('Fecha en la que ocurrio el siniestro');
            $table->date('report_date')->comment('Fecha en la que se reporto el siniestro');
            $table->date('close_date')->nullable()->comment('Fecha en la que se cerro el siniestro');
            $table->text('description')->comment('Descripcion del siniestro');
            $table->string('location')->comment('Ubicacion del siniestro');
            $table->string('status')->default(SinisterStatusEnum::REPORTED->value)->comment('Estado del siniestro');
            $table->foreignId('adjuster_id')->constrained('users')->restrictOnDelete()->comment('Identificador del ajustador');
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete()->comment('Identificador del supervisor');
            $table->foreignId('policy_id')->constrained('policies')->cascadeOnDelete()->comment('Identificador de la poliza');
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
