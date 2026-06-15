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
        Schema::create('assinaturas', function (Blueprint $table) {
            $table->id();
            // Vincula com a sua tabela de clientes
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            // Vincula com a sua nova tabela de planos
            $table->foreignId('plano_id')->constrained('planos')->onDelete('cascade');
            
            // 💳 CAMPOS PREPARATÓRIOS PARA PAGAMENTO FUTURO
            $table->string('gateway_id')->nullable(); // ID da transação na API (Stripe, Mercado Pago, etc.)
            $table->string('forma_pagamento')->default('Dinheiro/Balcão'); // Cartão, Pix, Boleto, etc.
            
            $table->date('data_inicio');
            $table->date('data_fim'); 
            
            $table->enum('status', ['Ativo', 'Inativo', 'Cancelado', 'Vencido'])->default('Inativo');
            $table->enum('status_pagamento', ['Pendente', 'Pago', 'Recusado', 'Estornado'])->default('Pendente');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assinaturas');
    }
};