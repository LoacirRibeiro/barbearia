<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('categoria'); // Cabelo, Barba, Combo, Facial & Cuidados, Química & Cores
            $table->decimal('preco', 8, 2);
            $table->boolean('ativo')->default(true); // Permite desativar um serviço sem apagá-lo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};