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
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->unique()->after('password'); // Adiciona 'cpf' após 'password'
            $table->foreignId('profile_id')->constrained('profiles')->after('cpf'); // Adiciona 'profile_id' após 'cpf'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['profile_id']); // Remover a chave estrangeira primeiro
            $table->dropColumn('profile_id');
            $table->dropColumn('cpf');
        });
    }
};