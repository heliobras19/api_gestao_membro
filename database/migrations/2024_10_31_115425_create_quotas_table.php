<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotas', function (Blueprint $table) {
            $table->id();
            $table->string('tipo');
            $table->float('valor');
            $table->integer('ano');
            $table->integer('mes');
            $table->foreignId('pagamento_id')->references('id')->on('pagamentos')->cascadeOnDelete();
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
        Schema::dropIfExists('quotas');
    }
}
