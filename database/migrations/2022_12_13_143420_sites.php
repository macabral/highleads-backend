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
        Schema::create('sites', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('pagina', 80)->unique();
            $table->string('responsavel', 80)->nullable();;
            $table->text('email');
            $table->string('telefone', 15)->nullable();;
            $table->tinyInteger('ativo')->default(1);
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
        Schema::dropIfExists('sites');
    }
};
