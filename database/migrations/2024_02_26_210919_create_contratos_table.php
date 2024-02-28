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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fornecedor_id')->constrained();
            $table->text('observacao')->nullable();
            $table->decimal('valor', 15, 2);
            $table->decimal('pago', 15, 2)->nullable();
            $table->string('tipo');
            $table->decimal('toneladas', 15, 2)->nullable();
            $table->integer('sacas')->nullable();
            $table->dateTime('data')->nullable();
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
        Schema::dropIfExists('contratos');
    }
};
