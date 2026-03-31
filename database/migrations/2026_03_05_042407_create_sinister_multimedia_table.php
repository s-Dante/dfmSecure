<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Enums\SinisterMultimediaTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinister_multimedia', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de multimedia de siniestros');
            $table->string('type')->comment('Tipo de multimedia');
            $table->binary('blob_file')->nullable()->comment('Archivo en formato binario');
            $table->longText('path_file')->nullable()->comment('Ruta del archivo');
            $table->text('description')->nullable()->comment('Descripcion del archivo');
            $table->string('mime')->nullable()->comment('Tipo MIME del archivo');
            $table->integer('size')->nullable()->comment('Tamaño del archivo');
            $table->string('thumbnail')->nullable()->comment('Miniatura del archivo');
            $table->foreignId('sinister_id')->constrained('sinisters')->comment('Identificador del siniestro');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE sinister_multimedia MODIFY blob_file LONGBLOB');
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sinister_multimedia');
    }
};
