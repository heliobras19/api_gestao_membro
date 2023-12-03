<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembroOrgaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('membro_orgaos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membro_id')->references('id')->on('membros')->cascadeOnDelete();
            $table->foreignId('orgao_id')->references('id')->on('orgaos')->cascadeOnDelete();
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
        Schema::dropIfExists('membro_orgaos');
    }
}
