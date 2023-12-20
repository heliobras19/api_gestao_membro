<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 50);
            $table->string('email', 50)->nullable();
            $table->string('telefone', 15);
            $table->char('sexo', 1);
            $table->date('data_nascimento')->nullable();
            $table->foreignId('comuna_id')->references('id')->on('comunas');
            $table->string('bi', 20)->unique();
            $table->string('pai')->nullable();
            $table->string('mae')->nullable();
            $table->string('estado_militante')->default("ativo");
            $table->integer('ano_ingresso')->nullable();
            $table->string('onde_ingressou')->nullable();
            $table->string('numero_membro')->nullable();
            $table->string('cartao_municipe')->nullable();
            $table->string('estrutura', 20);
            $table->foreignUuid('comite_id')->references('id')->on('comites');
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
        Schema::dropIfExists('membros');
    }
}
