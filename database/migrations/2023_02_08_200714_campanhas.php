<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campanhas', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('titulo', 120)->unique()->notNullable();
            $table->string('assunto,200');
            $table->longText('emailhtml');
            $table->tinyInteger('enviado')->default(0);
            $table->Integer('qtdemails')->default(0);
            $table->Integer('qtdcancelados')->default(0);
            $table->Integer('qtdvisitas')->default(0);
            $table->datetime('dtenvio')->default(null);
            $table->datetime('hrenvio')->default(null); 

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
        Schema::dropIfExists('campanhas');
    }
};
