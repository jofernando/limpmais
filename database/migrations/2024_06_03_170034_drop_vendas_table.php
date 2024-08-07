<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('motorista_venda');
        Schema::dropIfExists('vendas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
