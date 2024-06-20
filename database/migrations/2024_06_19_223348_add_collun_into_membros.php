<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollunIntoMembros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('membros', function (Blueprint $table) {
            $table->foreignId('bairro_residencia')->nullable()->references('id')->on('bairros');
            $table->string('natural_de', 100)->nullable();
            $table->string('nivel_academico', 50)->nullable();
            $table->string('partido_anterior', 70)->nullable();
            $table->boolean('is_quadro')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('membros', function (Blueprint $table) {
            //
        });
    }
}
