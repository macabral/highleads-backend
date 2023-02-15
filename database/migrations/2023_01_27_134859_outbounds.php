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
            $table->string('empresa', 120);
            $table->string('posicao', 120);
            $table->string('telefone', 120);
            $table->string('cidade', 120);
            $table->integer('usuarios_fk')->unsigned()->nullable()->default(null);
            $table->integer('categorias_fk')->unsigned()->nullable()->default(null);
            $table->tinyIntegerr('iscliente')->default(0);
            $table->tinyInteger('iscontato')->default(0);
            $table->tinyInteger('isvalid')->default(0);
            $table->tinyInteger('ativo')->default(1);

            $table->timestamps();
        
            $table->foreign('usuarios_fk')->references('id')->on('usuarios');
            $table->foreign('categorias_fk')->references('id')->on('categorias');
            
            $table->unique(['campanhas_fk','outbounds_fk']);

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
