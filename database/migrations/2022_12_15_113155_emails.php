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
        Schema::create('emails', function (Blueprint $table) {

            $table->integer('id', true)->unsigned();
            $table->string('para', 255);
            $table->string('cc', 255)->default('');
            $table->string('bcc', 255)->default('');
            $table->string('assunto', 255);
            $table->text('texto');
            $table->integer('erro')->default(0);
            $table->integer('enviado')->default(0);
            $table->string('anexos', 255)->default('');
            $table->tinyInteger('prioridade');
            $table->timestamps();
        
            $table->index('prioridade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
};
