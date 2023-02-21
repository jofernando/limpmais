<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('duplicatas', function (Blueprint $table) {
            $table->id();
            $table->decimal('valor', 15, 2);
            $table->decimal('pago', 15, 2)->nullable();
            $table->text('observacao')->nullable();
            $table->date('vencimento');
            $table->date('pagamento')->nullable();
            $table->foreignId('cliente_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('duplicatas');
    }
};
