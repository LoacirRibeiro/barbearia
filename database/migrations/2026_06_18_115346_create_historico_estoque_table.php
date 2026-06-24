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
        Schema::create('historico_estoque', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produto_id')->constrained('produtos')->onDelete('cascade');
        $table->foreignId('user_id')->constrained('users'); // Quem fez a ação (Admin ou Atendente)
        $table->enum('tipo', ['entrada', 'saida']); // Origem do movimento
        $table->integer('quantidade'); // Quantidade movimentada
        $table->string('motivo'); // Ex: "Venda Balcão #102" ou "Reposição de Estoque"
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_estoque');
    }
};
