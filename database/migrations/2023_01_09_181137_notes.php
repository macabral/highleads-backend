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
        Schema::create('notes', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->text('texto');
            $table->integer('contatos_fk')->unsigned();
            $table->integer('usuarios_fk')->unsigned();
            $table->timestamps();

            $table->foreign('contatos_fk')->references('id')->on('contatos')->onDelete('cascade');
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
        Schema::dropIfExists('notes');
    }
};
