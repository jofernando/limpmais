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
        Schema::table('duplicatas', function (Blueprint $table) {
            $table->foreignId('produto_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('tipo_quantidade')->nullable();
            $table->float('quantidade')->nullable();
            $table->integer('prazo')->nullable();
            $table->string('folguista')->nullable();
            $table->foreignId('motorista_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('outro')->nullable();
            $table->date('venda')->nullable();
            $table->date('entrega')->nullable();
            $table->foreignId('fornecedor_id')->nullable()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duplicatas', function (Blueprint $table) {
            $table->dropForeign(['produto_id']);
            $table->dropForeign(['motorista_id']);
            $table->dropForeign(['fornecedor_id']);
            $table->dropColumn([
                'produto_id',
                'tipo_quantidade',
                'quantidade',
                'prazo',
                'folguista',
                'motorista_id',
                'fornecedor_id',
                'outro',
                'venda',
                'entrega',
            ]);
        });
    }
};
