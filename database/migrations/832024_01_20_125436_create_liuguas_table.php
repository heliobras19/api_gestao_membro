<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiuguasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('liuguas', function (Blueprint $table) {
            $table->id();
            $table->string("lingua")->nullable();
            $table->enum("tipo", ["lingua", "idioma"])->default("lingua");
            $table->foreignId('membro_id')->references('id')->on('membros')->cascadeOnDelete();
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
        Schema::dropIfExists('liuguas');
    }
}
