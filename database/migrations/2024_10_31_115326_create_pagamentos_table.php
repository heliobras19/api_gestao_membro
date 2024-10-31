<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagamentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('referencia_pagamento')->nullable()->uniqid();
            $table->string('obs');
            $table->string('metodo_pagamento');
            $table->foreignId('processado_por');
            $table->date('data_pagamento');
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
        Schema::dropIfExists('pagamentos');
    }
}
