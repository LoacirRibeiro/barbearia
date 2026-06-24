<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Cria a coluna definindo 'repasse' como valor padrão para não quebrar os registros antigos
            $table->string('tipo_pagamento')->default('repasse')->after('valor');
        });
    }

    public function down(): void
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn('tipo_pagamento');
        });
    }
};