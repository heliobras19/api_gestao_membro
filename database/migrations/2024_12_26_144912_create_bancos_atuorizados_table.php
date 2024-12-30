<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBancosAtuorizadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bancos_atuorizados', function (Blueprint $table) {
            $table->id();
            $table->string('nome_banco');
            $table->string('nome_conta');
            $table->string('numero_conta')->unique();
            $table->string('iban')->unique();
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
        Schema::dropIfExists('bancos_atuorizados');
    }
}
