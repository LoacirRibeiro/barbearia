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
    Schema::create('caixas', function (Blueprint $table) {
        $table->id();
        $table->string('nome_cliente')->nullable(); // Adicionado para salvar o nome opcional
        $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
        $table->foreignId('servico_id')->nullable()->constrained('servicos')->onDelete('cascade');
        $table->string('forma_pagamento'); // Salva "Dinheiro", "Pix", etc.
        $table->decimal('valor_pago', 8, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixas');
    }
};
