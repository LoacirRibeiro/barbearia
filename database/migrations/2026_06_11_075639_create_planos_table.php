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
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: Plano Black, Combo VIP
            $table->string('slug')->unique(); // Para a URL (ex: plano-corte-de-cabelo)
            $table->decimal('preco', 8, 2); // Ex: 60.00, 105.00
            $table->text('descricao')->nullable(); // Detalhes do plano
            $table->integer('limite_cortes')->default(0); // 0 para ilimitado, ou ex: 4 para 4 vezes no mês
            $table->integer('limite_barba')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planos');
    }
};
