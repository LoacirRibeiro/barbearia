<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // 1. Controla os dias/turnos do caixa (Aberto, Fechado, Valores)
        Schema::create('caixa_sessoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Quem abriu o caixa
            $table->decimal('valor_abertura', 10, 2)->default(0.00); // Troco inicial
            $table->decimal('valor_fechamento_calculado', 10, 2)->default(0.00); // O que o sistema acha que tem
            $table->decimal('valor_fechamento_real', 10, 2)->nullable(); // O que o barbeiro contou fisicamente
            $table->decimal('diferenca', 10, 2)->default(0.00); // Sobra ou falta de dinheiro
            $table->enum('status', ['aberto', 'fechado'])->default('aberto');
            $table->timestamp('fechado_em')->nullable();
            $table->timestamps();
        });

        // 2. Registra saídas (sangrias) e entradas (suprimentos) de dinheiro da gaveta
        Schema::create('caixa_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_sessao_id')->constrained('caixa_sessoes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Quem fez a movimentação
            $table->enum('tipo', ['suprimento', 'sangria']); // entrada de troco ou saída manual
            $table->decimal('valor', 10, 2);
            $table->string('motivo'); // Ex: "Comprar pó de café" ou "Troco extra"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixa_sessoes_and_movimentacoes');
    }
};
