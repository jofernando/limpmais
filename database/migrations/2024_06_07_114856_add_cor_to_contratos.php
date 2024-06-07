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
        Schema::table('contratos', function (Blueprint $table) {
            $table->foreignId('cor_id')->nullable()->constrained();
            $table->foreignId('tamanho_id')->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropForeign(['cor_id']);
            $table->dropForeign(['tamanho_id']);
            $table->dropColumn(['cor_id', 'tamanho_id']);
        });
    }
};
