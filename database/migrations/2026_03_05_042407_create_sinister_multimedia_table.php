<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Enums\SinisterMultimediaTypeEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sinister_multimedia', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->binary('blob_file')->nullable();
            $table->longText('path_file')->nullable();
            $table->text('description')->nullable();
            $table->string('mime')->nullable();
            $table->integer('size')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('sinister_id')->constrained('sinisters');
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
