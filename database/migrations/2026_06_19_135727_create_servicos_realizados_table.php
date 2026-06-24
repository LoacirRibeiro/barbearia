<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos_realizados', function (Blueprint $table) {
            $table->id();
            // Vincula ao barbeiro que fez o trabalho
            $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
            
            // Dados do serviço prestado (salvamos o texto e preço para histórico imutável)
            $table->string('descricao');
            $table->decimal('preco', 10, 2);
            $table->decimal('comissao_valor', 10, 2); // Já calcula e grava o valor que ele ganha
            
            // Controle de Pagamento (nasce NULL, e vira o ID do pagamento quando você der baixa)
            $table->foreignId('pagamento_id')->nullable()->constrained('pagamentos')->onDelete('set null');
            
            // Opcional: Vincular ao item do caixa original se quiser rastrear a venda
            $table->unsignedBigInteger('caixa_item_id')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos_realizados');
    }
};