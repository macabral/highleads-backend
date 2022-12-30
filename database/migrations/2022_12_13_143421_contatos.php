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
        Schema::create('contatos', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('site', 255);
            $table->string('remoteip', 15);
            $table->datetime('datahora');
            $table->string('nome', 80);
            $table->string('email', 80);
            $table->string('telefone', 15);
            $table->timestamps();
            
            $table->index('email');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contatos');
    }
};
