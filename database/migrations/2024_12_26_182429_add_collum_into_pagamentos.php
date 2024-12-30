<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCollumIntoPagamentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        /*Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });*/

        // Adicionar a nova coluna com o relacionamento
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreignId('tipo')->nullable()->constrained('tipo_quotas')->nullOnDelete();
            $table->foreignId('banco_id')->nullable()->constrained('bancos_atuorizados')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            //
        });
    }
}
