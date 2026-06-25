<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
            $table->decimal('valor', 10, 2);
            $table->date('data_inicio_periodo'); // Início do filtro pago
            $table->date('data_fim_periodo');    // Fim do filtro pago
            $table->string('tipo_periodo');      // 'diario', 'semanal', 'mensal', 'avulso'
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};