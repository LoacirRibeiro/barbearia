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
    Schema::create('agendamentos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
        $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
        $table->dateTime('data_hora');
        $table->string('servico'); 
        $table->enum('status', ['Agendado', 'Concluído', 'Cancelado'])->default('Agendado');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
