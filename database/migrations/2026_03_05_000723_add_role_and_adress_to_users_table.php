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
        Schema::table('users', function (Blueprint $table) {
            //$table->foreignId('gender_id')->constrained('genders')->onDelete('cascade')->after('phone');
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete()->after('gender_id')->comment('Identificador del rol del usuario');
            $table->index('role_id');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->restrictOnDelete()->after('role_id')->comment('Identificador de la direccion del usuario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
            $table->dropForeign(['address_id']);
            $table->dropColumn('address_id');
        });
    }
};
