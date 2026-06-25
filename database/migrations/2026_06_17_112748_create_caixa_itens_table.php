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
        Schema::create('caixa_itens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id')->constrained('caixas')->onDelete('cascade'); // sua tabela atual de lançamentos
            $table->unsignedBigInteger('item_id');
            $table->string('tipo'); // 'servico' ou 'produto'
            $table->string('descricao');
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 8, 2);
            $table->decimal('subtotal', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caixa_itens');
    }
};
