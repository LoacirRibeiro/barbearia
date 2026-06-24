<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbeiros', function (Blueprint $table) {
            // Criamos um campo string com um valor padrão (default) para não quebrar os registros atuais
            $table->string('tipo')->default('colaborador')->after('nome'); 
        });
    }

    public function down(): void
    {
        Schema::table('barbeiros', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};