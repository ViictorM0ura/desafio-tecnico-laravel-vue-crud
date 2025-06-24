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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street');    // Rua
            $table->string('number')->nullable(); // Número (opcional)
            $table->string('complement')->nullable(); // Complemento (opcional)
            $table->string('neighborhood'); // Bairro
            $table->string('city');      // Cidade
            $table->string('state');     // Estado
            $table->string('zip_code');  // CEP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
