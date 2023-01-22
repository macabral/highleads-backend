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
        Schema::create('outbounds', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('nome', 80);
            $table->string('email', 80)->unique()->notNullable();
            $table->integer('usuarios_fk')->unsigned()->nullable()->default(null);
            $table->integer('iscliente')->default(0);
            $table->integer('iscontato')->default(0);
            $table->timestamps();
        
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
        Schema::dropIfExists('outbound');
    }
};