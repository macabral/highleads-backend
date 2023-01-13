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
            $table->string('remoteip', 20);
            $table->datetime('datahora');
            $table->string('nome', 80);
            $table->string('email', 80);
            $table->string('telefone', 15);
            $table->string('empresa')->nullable()->default(null);
            $table->integer('sites_fk')->unsigned()->nullable();
            $table->integer('usuarios_fk')->unsigned()->nullable()->default(null);
            $table->enum('status', [1,2,3,4,5])->default(1);
            $table->tinyInteger('score')->default(0);
            $table->timestamps();
            
            $table->index('email');
            $table->foreign('sites_fk')->references('id')->on('sites');
            $table->foreign('usuarios_fk')->references('id')->on('usuarios');

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
