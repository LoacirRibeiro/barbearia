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
        Schema::create('horarios_disponiveis', function (Blueprint $table) {
            $table->id();
            
            // Cria o vínculo com a tabela de barbeiros que você já tem (create_barbeiros_table)
            $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
            
            // Guarda a data e o horário do bloco (Ex: 2026-06-10 14:00:00)
            $table->dateTime('data_hora');
            
            // Controla se o horário está livre (true) ou se já foi agendado (false)
            $table->boolean('disponivel')->default(true);
            
            $table->timestamps();

            // Evita que o mesmo barbeiro tenha o mesmo horário cadastrado duas vezes
            $table->unique(['barbeiro_id', 'data_hora']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_disponiveis');
    }
};
