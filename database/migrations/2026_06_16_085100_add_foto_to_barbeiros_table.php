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
        Schema::table('barbeiros', function (Blueprint $table) {
            // Armazena o caminho relativo do arquivo da imagem
            $table->string('foto')->nullable()->after('especialidade');
        });
    }

    public function down(): void
    {
        Schema::table('barbeiros', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
