<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Enums\GenderEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('Identificador principal de la tabla de usuarios');
            $table->string('name')->comment('Nombre(s) del usuario');
            $table->string('father_lastname')->comment('Apellido paterno del usuario');
            $table->string('mother_lastname')->nullable()->comment('Apellido materno del usuario');
            $table->string('username', 30)->unique()->comment('Alias o identificador unico del usuario');
            $table->longText('profile_picture_url')->nullable()->comment('URL de la foto de perfil');
            $table->binary('profile_picture_blob')->nullable()->comment('Imagen de perfil en formato binario');
            $table->string('email')->unique()->comment('Correo electronico del usuario');
            $table->string('password')->comment('Contraseña del usuario');
            $table->string('phone', 20)->unique()->comment('Telefono del usuario');
            $table->date('birth_date')->nullable()->comment('Fecha de nacimiento del usuario');
            $table->string('gender')->default(GenderEnum::OTHER->value)->comment('Genero del usuario');
            // $table->tinyInteger('role_id')->constained('roles')->onDelete('cascade');
            $table->timestamp('email_verified_at')->nullable()->comment('Fecha en la que se verifico el correo electronico');
            $table->rememberToken()->comment('Token de recordatorio');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement('ALTER TABLE users MODIFY profile_picture_blob LONGBLOB');

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
